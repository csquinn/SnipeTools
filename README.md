# SnipeTools
SnipeTools is a web app that contains various tools for the Snipe IT Asset Management platform. These tools were designed specifically for my internship's Snipe IT implementation.

IMPORTANT! If you get a PHP error about a self-signed certificate with cURL, follow this guide https://php.watch/articles/php-curl-windows-cainfo-fix
the cacert.pm file is a file that contains many root CAs. Without it, cURL has no source to validate any certs with and will throw an error

Need to figure out some sort of authentication. Probably a group policy

Might have issues on unix system with \ and / in directories. remember to mention the user_variables folder

General Project information:

SnipeTools is a set of the three following tools

1. Asset Validation
	- Updates each scanned asset to synchronize specified fields

2. Return to Office
	- Marks each scanned asset as ready to deploy at the office, following all inventory conventions

3. Deprovision
	- Marks each scanned asset as deprovisioned at the office or storage, following all inventory conventions (any maybe also on Google Admin)

Each of the tools work in a similar way

1. Each tool can be accessed from the SnipeTools homepage
2. Each tool is run from a simple php file (validate.php, office.php, and deprovision.php) that hosts an html form designed to get an asset's serial number.
3. Those html forms then send a get request to getIDBySerial.php containing both the asset's serial as well as a variable that denotes which tool the serial came from.
4. getIDBySerial.php then sends an API call to Snipe IT to get an asset's internal Snipe ID based on its serial number.
5. If the asset doesn't exist, the user is routed back to the original tool php page with a failure message. Otherwise, they are routed to another php file that is strictly for api calls.
6. These api php files (validateAPI.php, officeAPI.php, and deprovisionAPI.php) perform one or more api calls to Snipe IT to update the asset.
7. Then, the user is routed back to the original tool php page with a success message. The html form is autofocused, meaning that the user can immediately begin typing or scanning the next asset.