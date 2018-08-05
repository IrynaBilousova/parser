# parser
Описание:
Данная программа предназначена для парсинга каталогов с листингом товаров с сайта m.ua. Парсер собирает основные данные со страницы каталога (категория, наименование товара, цена, описание), затем переходит на страницу с подробным описанием товара и собирает его свойства. Когда все товары со страницы обработаны, парсер переходит на следующую, если таковая имеется. Уже спарсенные товары обновляются. Парсер имеет свой файл с настройками, где нужно указать ссылки категорий, количество объектов, которые нужно спарсить и количество объектов для обновления. Парсер написан на php фреймворке Laravel версии 5.6.28 c использованием пакета symphony/dom-crawler для упрощения навигации по html документам.
Руководство по использованию:
Скачайте архив с репозитория: https://github.com/IrynaBilousova/parser
Выполните команду composer update для установки всех нужных пакетов
Переименуйте файл .env.example в .env и убедитесь, что соединение с базой данных установлено
Накатите миграции командой php artisan migrate. Укажите в файле app/Parser/config.php в массиве ‘urls’ все ссылки категорий, которые нужно парсить. Затем укажите parse_num – количество объектов для парсинга и update_num - максимальное количество объектов, которые будут обновлены. Запустите парсер командой php artisan parse.
