<?php
return [

    //model attributes
    'Username'=>'Логин',
    'Password'=>'Пароль',
    'New password'=>'Новый пароль',
    'Current password'=>'Текущий пароль',
    'Password repeat'=>'Повторите пароль',
    'New password repeat'=>'Повторите новый пароль',
    'Verification code'=>'Код проверки',
    'Status'=>'Статус',
    'Auth key'=>'Токен аутентификации',
    'Created time'=>'Время создания',
    'Updated time'=>'Время изменения',

    //model activate attributes
    'Аctivation method'=>'Способ активации',
    'Аctivation code'=>'Код активации',

    //model validator messages
    'The password can contain only English letters, numbers and symbols: {symbols}'=>'Пароль может содержать только английские буквы, цифры и знаки: {symbols}',
    'Username must start with an english letter and can contain only english letters, numbers and symbols: {symbols}'=>'Логин должен начинаться с англйской буквы, и может содержать только английские буквы, цифры и знаки: {symbols}',
    'The current password is incorrect.'=>'Текущий пароль задан неверно.',

    //statuses
    'inactive'=>'не активирован',
    'active'=>'активирован',
    'locked'=>'заблокирован',
    'deleted'=>'удален',

    //other
    'Registration'=>'Регистрация',
    'Register'=>'Зарегистрироваться',
    'Login'=>'Войти',
    'Logout'=>'Выход',
    'Activate'=>'Активировать',
    'My account'=>'Личный кабинет',

    'You have been registered on this site.'=>'Вы были зарегистрированы на сайте.',
    'Username and password to access the site sent to you email.'=>'Логин и пароль для входа на сайт высланы на email.',

    //activation
    'Account activation'=>'Активация аккаунта',
    'Select activation method:'=>'Выберите способ активации аккаунта.',
    'The message with the activation code sent to {uid}.'=>'Сообщение с кодом активации отправлено на {uid}.',
    'Send account activation code'=>'Отправить код активации учетной записи',
    'Account activation compleate successfully!'=>'Активация аккаунта выполнена успешно!',

    //authentication
    'Authentication accounts'=>'Идентификационные аккаунты',
    'Authentication account'=>'Идентификационный аккаунт',
    'Authentication account {type}:{uid} not found!'=>'Идентификационный аккаунт {type}:{uid} не подключен!',
    'Phone'=>'Номер телефона',
    'Type'=>'Тип',
    'Uid'=>'Уникальный идентификатор',
    'Activation code'=>'Код активации',
    'Activation code expire'=>'Время истечения срока кода активации',
    'Add account'=>'Подключить аккаунт',
    'Add new account: '=>'Подключить новый аккаунт: ',
    'New account successfully connected!'=>'Новый аккаунт успешно подключен!',
    'This account does not require activation (status: {status}).'=>'Этот аккаунт не требует активации (статус: {status}).',
    'Verification time is expired.'=>'Срок действия активации истек, повторите активацию.',
    'Verification code not set.'=>'Код активации не задан, повторите активацию.',
    'Verification code is not valid.'=>'Код активации не верен.',

    //login
    'Login'=>'Вход',
    'User not found.'=>'Пользователь не найден.',
    'Login or password is incorrect.'=>'Неверная пара логин-пароль.',
    'Remember me'=>'Запомнить меня',
    'Activation'=>'Активация',
    'Forgot password?'=>'Забыли пароль?',
    'Sign in is successful.'=>'Вход на сайт выполнен успешно.',
    'Sign in is failed.'=>'Не удолось выполнить вход на сайт.',

    //restore
    'Restore access'=>'Восстановить доступ',
    'Restore'=>'Восстановить',
    'Received a request to restore access for:'=>'Поступил запрос на восстановление доступа к сайту:',
    'To set a new password, please go to:'=>'Для установки нового пароля перейдите по ссылке:',
    'Reset code:'=>'Код сброса:',
    'Expire'=>'Действителен до',
    'If you did not request a password reset on {site}, just ignore this message.'=>'Если Вы не запрашивали сброс пароля на {site}, просто проигнорируйте данное сообщение.',
    'Reset token'=>'Код сброса',
    'Invalid reset token.'=>'Неверный код сброса.',
    'User with {type}:{uid} not found.'=>'Пользователь с аккаунтом {type}:{uid} не найден.',
    'User {username} not found.'=>'Пользователь {username} не найден.',
    'User account {type}:{uid} is not active.'=>'Аккаунт {type}:{uid} не активен.',
    'Account {type} can not be used to restore access.'=>'Аккаунт {type} не может быть использован для восстановления доступа.',
    'Reset password'=>'Сброс пароля',
    'Password reset token sent to {type}:{uid}.'=>'Код сброса пароля выслан на {type}:{uid}.',
    'Change password & Log in'=>'Сменить пароль и войти',
    'Password successfully changed.'=>'Пароль успешно изменен.',


    //admin
    'User'=>'Пользователь',
    'Users'=>'Пользователи',


    //user module
    'Sessions'=>'Сессии',
    'Account'=>'Личный кабинет',
    'Security'=>'Безопасность',
    'Change password'=>'Изменить пароль',
    'Disable Parallel Sessions'=>'Запретить паралельные сессии: при входе на новом устройстве выходить из других.',
    'Regenerate Auth key after log out'=>'При нажатии "Выход" - выходить со всех устройств.',
    'Connected accounts'=>'Подключенные аккаунты',

    'Profiles'=>'Профили',
    'Profile'=>'Профиль',

    'Your username: {username}'=>'Ваш логин: {username}',
    'Your password: {password}'=>'Ваш пароль: {password}',
];