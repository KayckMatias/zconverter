
# zConverter
A video converter made with Laravel, VueJs, Tailwind and Soketi due to study.


https://github.com/KayckMatias/zconverter/assets/48569093/99cd7eb2-a5be-4563-b892-5de04f56cbd0


## Prerequisites

- Laravel requirements
- MySQL or other database
- Node.js & npm
- [FFMpeg](https://www.ffmpeg.org/)
- [Soketi](https://docs.soketi.app)

# How to install?

Installation is very easy, like any Laravel project, just follow the steps:

- Install the composer dependencies
`composer i`
- Install npm dependencies
`npm i`
- Copy the .env.example to .env and configure
`cp .env.example .env`
- Generate a key
`php artisan key:generate`
- Run the migrations
`php artisan migrate`
- Seed the first user
`php artisan db:seed`
- Build the assets
`npm run prod`
- (optional) if run on localhost, start the laravel server
`php artisan serve`
- The project work with 3 queue, "downloader", "converter" and default, in the future I will implement horizon, but at the moment it will have to run the 3 queue.

`php artisan queue:work --queue=downloader --timeout=0`

`php artisan queue:work --queue=converter --timeout=0`

`php artisan queue:work`

Converter and Downloader queue uses timeout 0 because can exceed default 60 seconds limit.

Default mail: admin@admin.com

Default pass: password

# Comments about Project
This is a project for study or personal use, not intended for production.

In the future I will implement an automatic cleaning system to clean X files every X hours.

The download links were thought to be shareable with whoever the author wants, so there is no authentication in them, however, there are 3 levels of entropy (a uuid file, the uniqId in addition with file original name and a sha256 signature route), there is still the question of the ID of the conversation, making it impossible for any external person to access unless the author sends the link.

# Comments about Study
The initial intention was to be my first project in VueJS starting from 0, but I also saw an opportunity to learn about sockets. So I decided to build a video converter since it was possible to integrate the two in a meaningful way, in addition to learning and reinforcing several other things, such as using FFMpeg, queues in Laravel, entropy of links, etc.
