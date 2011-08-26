@echo off

rem Usage: minion [task:name] [--option1=optval1 --option2=optval2]
rem
rem And so on.
rem
rem To get help, pass in --help
rem
rem Minion general help
rem minion --help
rem minion
rem
rem Task specific help
rem minion task:name --help

@setlocal

set TASK=""

if not "%*"=="" set TASK=--task=%*

set KOHANA_PATH=%~dp0

"php" "%KOHANA_PATH%index.php" --uri=minion %TASK%

@endlocal