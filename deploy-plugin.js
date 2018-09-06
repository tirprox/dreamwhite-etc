const sftp = require('node-sftp-deploy');
const path = require('path');
const auth = require ('./auth');

const inProduction = process.argv[3] === 'live';
const name = process.argv[2];

let config = {
    host: "dreamwhite.ru",
    port: "22",
    user: auth.login,
    pass: auth.password,
};

const base = {
    test: '/var/www/vhosts/dreamwhite.ru/new.dreamwhite.ru/wp-content/plugins/',
    live: '/var/www/vhosts/dreamwhite.ru/dreamwhite.ru/wp-content/plugins/'
};

config.remotePath = inProduction
    ? base.live + name
    : base.test + name;

config.sourcePath = path.resolve('./plugins', name);

sftp(config, function(){
    console.log('OK');
});
