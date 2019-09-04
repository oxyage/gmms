echo off
chcp 1251
echo "IP адрес: 10.32.1.2 | NetworkID: вход 1; вход 2;"
snmpget -m 0  -L n -c private -v 2c -t 8 10.32.1.2:8001 1.3.6.1.4.1.22909.1.3.13.1.3.1.1.1
snmpget -m 0  -L n -c private -v 2c -t 8 10.32.1.2:8001 1.3.6.1.4.1.22909.1.3.13.1.3.1.1.2


pause


