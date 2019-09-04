echo off
for /L %%i in (1,1,31) do (
echo "IP: 10.32.%%i.2 | Main Delay"
snmpget -m 0  -L n -c private -v 2c -t 8 10.32.%%i.2:8001 1.3.6.1.4.1.22909.1.3.15.1.1.1.11.9
echo "IP: 10.32.%%i.2 | Leading Source"
snmpget -m 0  -L n -c private -v 2c -t 8 10.32.%%i.2:8001 1.3.6.1.4.1.22909.1.3.15.1.1.1.12.9
echo "IP: 10.32.%%i.2 | Source Delay"
snmpget -m 0  -L n -c private -v 2c -t 8 10.32.%%i.2:8001 1.3.6.1.4.1.22909.1.3.15.1.1.1.13.9
echo.
)
pause