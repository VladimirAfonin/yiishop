"use strict";
var page = require('webpage').create(),
    system = require('system'),
    address, delay;

if (system.args.length < 3 || system.args.length > 5) {
    console.log('Usage: delay.js URL delay');
    phantom.exit(1);
} else {
    address = system.args[1];
    delay = system.args[2];
    page.open(address, function (status) {
        if (status !== 'success') {
            console.log('Unable to load the address!');
            phantom.exit(1);
        } else {
            window.setTimeout(function () {
                var content = page.content;
                console.log(content);
                phantom.exit();
            }, delay);
        }
    });
}