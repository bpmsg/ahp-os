# AHP-OS

AHP-OS is a php program package for the Analytic Hierarchy Process, a 
mathematical tool to support rational decision making. It is an online
tool written in php with a web browser interface, where users can 
register, login and define their own decision hierarchies.
Example: [link](https://bpmsg.com/ahp/)

Php package (c) Klaus D Goepel, 2013-2022

## AHP-OS Features

* Flexible definition of decision hierarchies as text input, following
  a simple syntax with multi-language support using Unicode character 
  coding,
* Weight calculation (hierarchy mode) and alternative evaluation 
 (alternative mode) using the AHP eigenvector method,
* Pairwise comparison input, highlighting the top-3 most inconsistent 
  judgments,
* Partial judgments,
* A posteriori application of different AHP judgment scales,
* Group decision making using weighted geometric mean aggregation of
* individual judgments (WGM-AIJ),
* Group consensus calculation based on Shannon α and β-entropy,
* Weight uncertainty estimation using Monte Carlo simulation,
* Sensitivity analysis,
* Weighted sum model (WSM) and weighted product model (WPM) for the
  aggregation of alternatives,
* Export of input and result data as comma separated value (CSV) files
  for further processing or presentation in a spreadsheet program

## Installation

Create a folder in your web root directory, e.g. `ahp`.
Copy all files - keeping the directory structure - into this folder.
Modify includes/config.php to set your database and mail parameters.
As a database, either `sqlite` or `mysql` (MariaDB) can be defined.
If you use `mysql`, create an empty database first.
Run `db/ahp-sql-create.php` to create the necessary tables and triggers.

## Usage

Run your web browser and open `http://your-web-root/ahp/`

A complete manual and quick reference guide can be found in the `docs` 
folder. Mathematical background and details about the implementation
are published in:

> Goepel, K.D. (2018). Implementation of an Online Software Tool for 
> the Analytic Hierarchy Process (AHP-OS). 
> International Journal of the Analytic Hierarchy Process, 
> Vol. 10 Issue 3 2018, pp 469-487,
> [link](https://doi.org/10.13033/ijahp.v10i3.590)

In order to use the package, you need to create a user account
by registering on the web interface. The `ADMIN` user is defined 
by the user id given in the `config.php` file.

## User administration

The package allows to administer users. Users, not active over a period
of 90 days, can be deactivated and an optional email for reactivation
will be sent. If their account is not activated within 48 hours, it can 
be deleted by the admninistrator.

Donations can be tracked, and the above deactivation will not apply to 
donors.

## Limitations

Defined in config.php:
* WLMAX   = 45 word length of nodes and leafs
* CRITMAX = 20 max. number of criteria per node
* ALTAHP  = 20 number of alternatives
* SESSIONLMT = 50 number of projects per user
* NVAR = 1000 variaton of judgments (Monte Carlo)

Defined in class ahphier.php:
* TXT_MAX = 6000 characters defining the hierarchy
* NODE_CNT = 50 number of nodes
* LEAF_MAX = 100 number of leafs in the hierarchy

## Directory structure

The working directory (`ahp`) should be a subdirectory of
the web root directory.

```
ahp-\
     |-- classes-\
     |           AhpCalc (Eigenvector of DM)
     |           AhpCalcIo (extends AhpCalc, html/csv i/o)
     |           AhpColors (html coloring)
     |           AhpDb (database functions)
     |           AhpGroup (group result calculation)
     |           AhpHier (Decision hierarchies)
     |           AhpHierAlt (extends AhpHier for alternatives)
     |           (JsCheck) by Gustav Eklundh
     |           WebHtml (header/footer for html output)
     |-- db -\ 
     |        ahp_os.db (sqlite database when used)
     |        ahp-sql-create (for installation only,
     |                        generates sql tables & triggers)
     |--includes-\
     |            config.php (Package Configuration)
     |            footer.html
     |            header.html
     |            showCaptcha.php
     |            style.css (style sheet)
     |            fonts --- times_new_yorker.ttf
     |            |
     |            |--login ------AhpAdmin (class, extends LoginAdmin)
     |            |              form.donations
     |            (phpgraphlib)  form.edit
     |-- docs     (PHPMailer)    form.login-hl
     |                           form.newdon
     |-- images -- AHP-icon      form.registration
     |             ahp-os-icon   form.UserAdminMenu
     |                           Login.php (class)
     |--js -- ahp-group          LoginAdmin.php (class, extends Login)
     |        ahp-session-admin  LoginDE/EN/ES/PT (language texts)
     |        btnr               Registration.php (class)
     |        cfm                |
     |        cfdef               \
     |        delcfm               do -dbIntegrity
     |        lvecfm                   do-donor-admin
     |        sh-part                  do-edit
     |        webFont                  do-log
     |                                 do-register
     |                                 do-reset-pw
     |                                 do-user-admin (user administr.)
     |
     |--language -- de/en -- AhpCalcXX, AhpDbXX, AhpXX, AhpGroupXX
     |              es/pt    AhpGroupResXX, AhpHierarchyXX,
     |                       AhpHierXX, AhpHierginiXX, AhpNewsXX,
     |--views (html menus)   AhpPrioCalcXX, AhpSessionAdminXX
```

## License
The work is published under GNU GENERAL PUBLIC LICENSE Version 3.

## Work in progress!

2022-01-28 - First version added to svn
