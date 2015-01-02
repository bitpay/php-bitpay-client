#!/usr/bin/env bash
set_variables ()
{
	while true
		do
			read -p "Input Email: " EMAIL
			regex="^[a-z0-9!#\$%&'*+/=?^_\`{|}~-]+(\.[a-z0-9!#$%&'*+/=?^_\`{|}~-]+)*@([a-z0-9]([a-z0-9-]*[a-z0-9])?\.)+[a-z0-9]([a-z0-9-]*[a-z0-9])?\$";
			if [[ "$EMAIL" =~ $regex ]]
			then
				export BITPAY_EMAIL=$EMAIL
				break
			else
				echo "Please input a valid email"
			fi
		done
	while true
		do
			read -p "Input Password: " PASSWORD
			read -p "Password Confirmation: " PASSWORD2
			if [ "$PASSWORD" = "$PASSWORD2" ]
			then
				break
			else
				echo "Please input a valid password"
			fi
		done
	while true
		do
			read -p "Input URL: " URL
			if [ -z $URL ]
			then
				echo "Please input a valid URL"
			else
				break
			fi
		done
}

if [ -z "$1" ]
then
	echo "No parameters passed so using Environment Variables"
	if [ -z "$BITPAY_EMAIL" ] || [ -z "$BITPAY_PASSWORD"]
	then
		echo "ERROR: No Email or password are set."
		echo "set BITPAY_EMAIL and BITPAY_PASSWORD as environment variables"
		echo "or pass them as arguments when running this script"
		while true; do
			read -p "Do you wish to set your environment variables here? " yn
			case $yn in
				[Yy]* ) set_variables; break;;
				[Nn]* ) echo "Closing script"; exit;;
				* ) echo "Please answer yes or no.";;
			esac
		done
	else
		echo "Environment Variables already exist for BITPAY."
	fi
else
	echo "Username $1 and Password $2 passed from command line"
	URL=$1
	EMAIL=$2
	PASSWORD=$3
	echo "Setting user and Password to new environment variables..."

fi

export BITPAY_EMAIL=$EMAIL
export BITPAY_PASSWORD=$PASSWORD
export BITPAY_URL=$URL
echo "Using Email: $EMAIL"
echo "Using URL: $URL"

echo "Removing old keys..."
if [ -e /tmp/bitpay.pub ]
then
	rm -rf /tmp/bitpay.pub
	rm -rf /tmp/bitpay.pri
	rm -rf /tmp/token.json
fi

echo "Checking if Selenium exists..."
if [ ! -f selenium-server-standalone-2.44.0.jar ]
then
	echo "Downloading Selenium"
	curl -O http://selenium-release.storage.googleapis.com/2.44/selenium-server-standalone-2.44.0.jar
fi

echo "Running Selenium and the tests"
php bin/behat tests/integrations