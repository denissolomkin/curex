Guide
=====

1) Перейти в папку, в которой будет размещаться проект и выполнить команду:
`git clone https://github.com/denissolomkin/curex.git curex`

2) Перейти в папку с проектом выполнить команду, в процессе установки задать указать
подключения к БД:
`composer update`

3) Выполнить команду на создание базы (если базы еще нет, если уже создана, пропускаем):
`php bin/console doctrine:database:create`

5) Выполнить команду для создания таблиц
`php bin/console doctrine:schema:create`

6) Выполнить команду для загрузки тестовых данных [в процессе]:
`php bin/console doctrine:fixtures:load `

7) Для ежедневной синхронизации курсов в полночь добавляем команду, поменяв при необходимости пути, в крон (crontab -e)
`0 0 * * *  /usr/bin/php /var/www/curex/bin/console app:sync-rates  >>  /var/log/cron 2>&1`

8) Выполнить команду, если не хотим ждать синхронизации курсов по наступлению времени в кроне:
`php bin/console app:sync-rates`

9) Открыть проект в браузере или выполнить команды для отображения запросов:
`php bin/console app:get-payments`
`php bin/console app:get-statistics`