=======
Testing
=======

Please make sure that your code is tested using phpunit. To install phpunit
make sure you have composer installed and just install the dev packages.

.. code-block:: bash

    composer.phar install

.. note::

    By default Composer will install the development packages. You should
    not need to run ``install`` or ``update``.

Next you will just need to run phpunit. To run phpunit, run the command:

.. code-block:: bash

    php bin/phpunit -c build/

This will run the tests and will also generate the code coverage reports which
you can view by opening the HTML files generated in the ``build/docs/code-coverage``.
