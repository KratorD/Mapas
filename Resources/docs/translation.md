# TRANSLATION INSTRUCTIONS

To create a new translation follow the steps below:

1. First install the module like described in the `install.md` file.
2. Open a console and navigate to the Zikula root directory.
3. Execute this command replacing `en` by your desired locale code:

`php bin/console translation:extract en --bundle=TdMMapsModule --enable-extractor=jms_i18n_routing --output-format=po`

You can also use multiple locales at once, for example `de fr es`.

4. Translate the resulting `.po` files in `modules/TdM/MapsModule/Resources/translations/` using your favourite Gettext tooling.

Note you can even include custom views in `app/Resources/TdMMapsModule/views/` and JavaScript files in `app/Resources/TdMMapsModule/public/js/` like this:

`php bin/console translation:extract en --bundle=TdMMapsModule --enable-extractor=jms_i18n_routing --output-format=po --dir=./modules/TdM/MapsModule --dir=./app/Resources/TdMMapsModule`

For questions and other remarks visit our homepage https://www.heroesofmightandmagic.es.

Krator (torredemarfil@heroesofmightandmagic.es)
https://www.heroesofmightandmagic.es
