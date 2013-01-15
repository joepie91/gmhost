The code that powers [GMHost](http://gmhost.cryto.net/).

**WARNING:** Extremely messy, bad, and hacky code. Use at your own risk.

Requirements: 

* HTTPd (preferably something non-Apache)
* PHP 5.3+
* PHP-cURL
* Tahoe-LAFS

GMHost uses [CPHP](http://github.com/joepie91/cphp). A symlink is used in the repository to point at where CPHP could be. You'll probably want to either put CPHP in the expected location,
or change the symlink. Copy `config.json.example` to `config.json` and set a public directory writecap. The private directory writecap is currently unused.

This code is licensed under the [WTFPL](http://wtfpl.net/). Copy, redistribute, hack and remix as you wish. [Credit or donation](http://cryto.net/~joepie91/donate.html) appreciated, neither required.

Icons provided by [famfamfam](http://www.famfamfam.com/lab/icons/silk/). These are under a [Creative Commons Attribution 2.5 license](http://creativecommons.org/licenses/by/2.5/).
