# Minimalistic File Sharing Service (like 0x0.st)

Этот проект — простой и минималистичный сервис для быстрой загрузки и раздачи файлов по аналогии с https://0x0.st 
Позволяет загружать файлы через `curl` и получать ссылки для скачивания и удаления.

---
## Стек
- PHP 8.3+
- Laravel 12
- SQLite (встроенная база данных)
- (Опционально) Docker + docker-compose для локального запуска
## Функционал
- Загрузка файла через `curl -F "file=@filename" https://yourdomain.com`
- В ответе HTTP 200 тело содержит ссылку для скачивания, заголовок `X-Delete` — ссылку для удаления
- Скачивание файла через `curl https://yourdomain.com/file/{hash}`
- Удаление файла через `curl https://yourdomain.com/delete/{delete_hash}`
- Автоматическое удаление файлов после истечения времени жизни (по умолчанию 24 часа)
- Простой текстовый гайд по загрузке доступен по GET запросу к `/`
---

## Установка и запуск
### 1. Клонировать репозиторий
git clone https://github.com/yourusername/yourrepository.git
cd yourrepository

### 2. Установить зависимости Composer
composer install

### 3. Создать файл окружения `.env`
Скопируйте из шаблона:
cp .env.example .env
Отредактируйте `.env`, если нужно (особенно `APP_URL`).

### 4. Сгенерировать ключ приложения
php artisan key:generate

### 5. Создать и подготовить базу данных SQLite
touch database/database.sqlite
php artisan migrate

### 6. Запустить разработческий сервер
php artisan serve
Или с Docker (если есть `docker-compose.yml`):
docker-compose up

## Использование
### Загрузка файла
curl -F "file=@yourfile.ext" http://localhost:8000 -v

В ответ:
- В теле — ссылка для скачивания, например:  
  `http://localhost:8000/file/abc123`
- В заголовке `X-Delete` — ссылка для удаления файла, например:  
  `http://localhost:8000/delete/xyz456`

### Скачивание файла
curl http://localhost:8000/file/abc123

### Удаление файла
curl http://localhost:8000/delete/xyz456

## Автоматическое удаление устаревших файлов
Есть команда Artisan, которая удаляет все файлы, время жизни которых истекло:
php artisan files:cleanup
Чтобы запускать её автоматически, добавьте в планировщик задач в `app/Console/Kernel.php`:
protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
{
$schedule->command('files:cleanup')->everyFiveMinutes();
}
И запустите планировщик:
php artisan schedule:work


## Структура проекта
- `app/Http/Controllers/FileController.php` — основная логика приложения
- `app/Models/File.php` — модель для хранения файлов
- `database/migrations/` — миграция для таблицы файлов
- `routes/web.php` — маршруты загрузки, скачивания, удаления и гайд
- `docker-compose.yml` — для запуска через Docker
- `storage/app/uploads/` — директория для сохранения загруженных файлов

## Лицензия

MIT

Если возникнут вопросы или пожелания — обращайтесь!

