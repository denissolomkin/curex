<?php

namespace AppBundle\Command;

use AppBundle\Entity\CurrencyRate;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SyncRatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:sync-rates')
            ->setDescription('Sync rates.')
            ->setHelp('Get rates from MasterCard and save its into database for last 10 days')
            ->addArgument(
                'days',
                InputArgument::OPTIONAL,
                'How many days need to sync?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {


        $em = $this->getContainer()->get('doctrine')->getManager();

        /* наши настройки, при желании*/
        $currencies = array('EUR', 'USD');
        $baseCurrency = 'UAH';
        $days = $input->getArgument('days')?:7; /* глубина выборки дней */
        $tries = 2; /* в случае отсутствия курса у МастерКарда для первого дня, сколько дней перебрать до него */
        $dateEnd = new \DateTime('now');

        /* фиксим параметры, переводим значения с человекопонятных в алгоротмоориентированные */
        $days--;
        $tries++;

        /* тут мы показываем, что знаем и любим замыкания */
        $getRate = function ($currency, $date) use ($baseCurrency) {
            $url = 'https://www.mastercard.us/settlement/currencyrate/%s/conversion-rate';
            $data = array(
                'fxDate' => date_format($date, 'Y-m-d'),
                'transCurr' => $currency,
                'crdhldBillCurr' => $baseCurrency,
                'transAmt' => 1,
                'bankFee' => 0
            );

            $url = sprintf($url, http_build_query($data, "", ";"));

            return json_decode(file_get_contents($url), true);

        };

        $output->writeln(['Sync Rates', '============',]);

        foreach ($currencies as $currency) {

            $output->writeln('Currency : ' . $currency);

            /* для каждой валюты устанавливаем начальный день заново */
            $dateStart = new \DateTime('now');
            $dateStart->modify('-' . $days . ' day');

            /* обнуляем последний найденный курс, понадобится для дней, по которым МастерКард жадничает отдачу */
            $lastRate = null;

            /* заново переустанавливаем количество попыток для новой валюты */
            $currentTries = $tries;

            do {

                /* постучались к МастерКард */
                $result = $getRate($currency, $dateStart);

                /* если у нас имеется последний курс или МастерКард отдал курс за этот день */
                if ($lastRate || !key_exists('type', $result)) {

                    $output->write($dateStart->format('Y-m-d') . ' = ');

                    /* если последний курс был еще не переобъявлен, то устанавливаем начальный день заново,
                    мало ли сколько дней мы пролистали назад, пока МастерКард нам таки отдал курс */
                    if (is_null($lastRate)) {
                        $dateStart = new \DateTime('now');
                        $dateStart->modify('-' . $days . ' day');
                    }

                    /* если есть свежий курс, то обновляем последний найденный на него */
                    if (key_exists('conversionRate', $result['data'])) {
                        $lastRate = $result['data']['conversionRate'];
                    }

                    /* проверяем, а не сохраняли ли мы его в прошлой жизни */
                    $currencyRate = $em->getRepository('AppBundle:CurrencyRate')->findOneBy([
                        'currencyCode' => $currency,
                        'date' => $dateStart
                    ]);

                    /* если не нашли, то создаем.
                       если же нашли - пропускаем / пересохраняем (в данном примере пропускаем) */
                    if (is_null($currencyRate)) {
                        $currencyRate = new CurrencyRate();
                        $currencyRate->setDate($dateStart)
                            ->setCurrencyCode($currency)
                            ->setRate($lastRate);
                        $em->persist($currencyRate);
                        $em->flush();

                        if (key_exists('conversionRate', $result['data'])) {
                            $output->writeln($lastRate);
                        } else {
                            $output->writeln($lastRate . '[use previous day]');
                        }

                    } else {
                        $output->writeln($currencyRate->getRate() . '[already exists]');
                    }

                    /* если всё прошло успешно, переходим к следующему дню */
                    $dateStart->modify('+1 day');

                } else {
                    /* а если не успешно, то пробуем найти курс за прошлый день, попутно уменьшая наши попытки */
                    $currentTries--;
                    $dateStart->modify('-1 day');
                }


                /* продолжаем, пока есть попытки или не прошлись по всем дням */
            } while ($currentTries && $dateStart->diff($dateEnd)->format("%R%a") >= 0);

            /* если попыток не осталось, значит курс найти несмотря на все наши усилия так и не удалось,
               посмотрим, что скажет МастерКард в свое оправдание по этому поводу */
            if (!$currentTries) {
                throw new NotFoundHttpException("$currency error: {$result['data']['errorMessage']}");
            }

        }
    }
}