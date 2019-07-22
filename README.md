
Инструкция по развертыванию Olymp ERP в Linux
=============================================

1. Установить MySQL для хранения данных сайта. Открыть консоль, ввести команду:
>sudo apt-get install mysql-server
2. Установить PHP, ввести команду:
>sudo apt-get install php-fpm php-mysql
3. Установить необходимые для работы Symfony пакеты PHP:
>sudo apt-get install php-bcmath
>sudo apt-get install php-xml 
>sudo apt-get install php-mbstring 
4. Запустить скрипт композера на установку:
>php composer.phar install
5. Указать pdo_mysql в качестве параметра для database_driver.
   database_host и database_port оставляем без изменений. 
   database_name: olymp
   database_user можно оставить root или указать свой.
   Обязательно указываем пароль в параметре database_password.
   Все последующие поля до завершения работы композера можно оставить 
   пустыми (будут приняты стандартные значения).
6. Проверить права доступа на запуск скриптов из папки bin/console, 
   если текущий пользователь не может их запускать, сделать их 
   возможными для запуска:
>(находясь в папке bin)chmod 0755 ./console 
7. Создать схему БД:
>php bin/console doctrine:database:create
8. Сгенерировать сущности в БД:
>php bin/console doctrine:migrations:migrate
9. Запустить сервер:
>php bin/console server:start -d web
10. Создать пользователя-суперадмина для работы с Олимпом:
>php bin/console fos:user:create --super-admin
11. Залогиниться в админку Олимп (стандартный адрес: 127.0.0.1:8000/admin) и настроить рабочую группу пользователя.
    Для этого нужно в левом меню выбрать элемент Team -> Отделы. На открывшейся странице кликнуть по кнопке "Добавить новый". На новой странице указать Заголовок (название группы) и Код (произвольное число). После заполнения нажать кнопку "Создать и добавить новый". Затем, в левом меню перейти во вкладку пользователи. Выбрать своего пользователя, щелкнув по его имени. В поле Team в конце страницы указать группу.  
    
Установка Olymp ERP завершена, можно работать с системой.
 
Инструкция по развертыванию Olymp ERP в Windows
=============================================

1. Установить Php (http://windows.php.net/download#php-7.2)
2. Установить PhpStorm (https://www.jetbrains.com/phpstorm/)
3. Сгитить проект Олимп ERP.
4. Запустить PhpStorm из папки Олимпа.
5. Указать интерпретатор для проекта в шторме (установленный Php).
6. Запустить композер из Шторма:  
   правой кнопкой по composer.json -> Composer -> Install. Выбрать Composer.phar из списка, ждать пока установятся все паки.
7. Установить MySQL, обязательно указываем и запоминаем пароль <DATABASE_PASSWORD> и логин <DATABASE_USER> администратора.
8. Поднять сервер MySQL.
9. Создать базу данных с именем <БАЗА ДАННЫХ>.
10. Поправить конфигурационный файл olymp\app\config\parameters.yml:
    database_driver: pdo_mysql
    database_host: 127.0.0.1
    database_port: null
    database_name: <БАЗА ДАННЫХ>
    database_user: <DATABASE_USER>
    database_password: <DATABASE_PASSWORD>
11. Запустить командную строку (cmd или PowerShell) и сконфигурировать созданную схему БД:
    php bin/console doctrine:database:create
12. Сгенерировать сущности в БД:
    php bin/console doctrine:schema:update --force
    (В новой версии с миграциями):
    php bin/console doctrine:migrations:migrate
13. В новой командной строке создать пользователя-суперадмина для работы с Олимпом:
    php bin/console fos:user:create --super-admin
14. Ввести логин и пароль супер-пользователя.    

Дальше по аналогии с Линукс.

Инструкция по просмотру mail сообщений в Windows
=============================================
1. Скачать Papercut.Setup.exe по ссылке https://github.com/ChangemakerStudios/Papercut/releases
2. Установить


Инструкция по просмотру mail сообщений в Linux
=============================================
1. Установить postfix:
>sudo apt-get install postfix

При установке выбрать Internet site, в поле System mail name пишем smtp.yandex.ru.
2. Открываем в любом редакторе (nano, gedit) файл конфигурации postfix'a:
>sudo gedit /etc/postfix/main.cf

Проверяем следущие строки:

myhostname = smtp.yandex.ru

mydestination = $myhostname, 127.0.0.1, smtp.yandex.ru, localhost.yandex.ru, localhost

В mynetworks дописываем 192.168.0.0/22 (Если у вас до этого все хорошо, можете пропустить этот пункт)

3. Устанавливаем mailutils:
>sudo apt-get install mailutils

Для проверки используйте эту команду:

echo "Тест" | mail -s "Тест" ваша_почта@*.com    - почта на которую хотите, чтобы пришли сообщения.

Инструкция по возвращению потерявшихся заявок
=============================================
1. Для нахождения ВСЕХ потерявшихся заявок выполните команду:
>php bin/console olymp:request:actualize

2. Команда на вход принимает параметр - сколько заявок обработать, пример:
>php bin/console olymp:request:actualize 3 // Найдет и вернет первые 3 заявки

3. Все найденные заявки будут "На исправлении".

Инструкция по созданию запланированных задач
=============================================
1. Для создания запланированных на сегодня зачач выполните команду:
>php bin/console olymp:scheduler:create'

2. Все запланированные на сегодня задачи будут созданы.

Инструкция по импорту номенклатуры и классификатора ЕСКД
=============================================
1. Изменить upload_max_filesize и post_max_size в php.ini:

2. Изменить max_input_time = 0 (0 - чтобы не было ограничения по времени работы скрипта).