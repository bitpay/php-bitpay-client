##  Contributing
If you are looking to contribute to this project, you have come to the
right place. This project needs people that are willing to add
documentation, code, or anything else.

Fork Project
============

First step is to fork this project. Visit the projects GitHub page and
click the fork button.

Clone Your Fork
===============

``` {.sourceCode .bash}
git clone git@github.com:USERNAME/php-client.git && cd php-client
```

Add upstream Remote
===================

Adding a remote helps keep you up to date with the lastest code updates.

``` {.sourceCode .bash}
git remote add -f upstream git@github.com:bitpay/php-client.git
```

Creating Branches
=================

Please checkout the branch that you want to work on. For example the
latest code is located on the master branch, so make sure you checkout
that branch. Once you are on that branch, cut a new branch with a
descriptive name.

``` {.sourceCode .bash}
git checkout master
git checkout -b feature/MyNewFeature
```

> **note**
>
> other branch names COULD include: test/AddedTest,
> hotfix/ThisFixesBroken, remove/DoesnotBelongHere,
> doc/AddedDocumentation. Notice the type of branch at the start.

Push and Submit Pull Request
============================

Push the branch up and submit a pull request. Please make sure that you
have correct code comments and the code has been formatted to PSR-1 and
PSR-2 standards.

Contributing to Documentation
=============================

Documentation is generated with [Sphinx](http://sphinx-doc.org) and can
easily be updated. Once you have Sphinx installed, you just need to run
the command "make html" in the docs/ directory and view the output in
the \_build directory. It's all HTML files so you should not have any
issues testing.
