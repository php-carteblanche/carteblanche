#!/bin/bash
#
# This file is part of the CarteBlanche PHP framework.
#
# (c) Pierre Cassat <me@e-piwi.fr> and contributors
#
# License Apache-2.0 <http://github.com/php-carteblanche/carteblanche/blob/master/LICENSE>
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# CGI-script to debug Apache env
#

## Config
CHARSET='utf-8'

## debug
echo "Content-type: text/html;charset=${CHARSET}"
echo
echo "<h1>Apache debug <em>(all apache environment variables)</em></h1>"
echo "<p>For more infos, see: <a href=\"http://httpd.apache.org/docs/\">http://httpd.apache.org/docs/</a>.</p>"
echo "<h2>HTTP headers</h2>"
echo "<ul>"
echo "<li>HTTP_USER_AGENT : $HTTP_USER_AGENT</li>"
echo "<li>HTTP_REFERER : $HTTP_REFERER</li>"
echo "<li>HTTP_COOKIE : $HTTP_COOKIE</li>"
echo "<li>HTTP_FORWARDED : $HTTP_FORWARDED</li>"
echo "<li>HTTP_HOST : $HTTP_HOST</li>"
echo "<li>HTTP_PROXY_CONNECTION : $HTTP_PROXY_CONNECTION</li>"
echo "<li>HTTP_ACCEPT : $HTTP_ACCEPT</li>"
echo "</ul>"
echo "<h2>Connection & request</h2>"
echo "<ul>"
echo "<li>REMOTE_ADDR : $REMOTE_ADDR</li>"
echo "<li>REMOTE_HOST : $REMOTE_HOST</li>"
echo "<li>REMOTE_PORT : $REMOTE_PORT</li>"
echo "<li>REMOTE_USER : $REMOTE_USER</li>"
echo "<li>REMOTE_IDENT : $REMOTE_IDENT</li>"
echo "<li>REQUEST_METHOD : $REQUEST_METHOD</li>"
echo "<li>SCRIPT_FILENAME : $SCRIPT_FILENAME</li>"
echo "<li>PATH_INFO : $PATH_INFO</li>"
echo "<li>QUERY_STRING : $QUERY_STRING</li>"
echo "<li>AUTH_TYPE : $AUTH_TYPE</li>"
echo "</ul>"
echo "<h2>Internal server variables</h2>"
echo "<ul>"
echo "<li>DOCUMENT_ROOT : $DOCUMENT_ROOT</li>"
echo "<li>SERVER_ADMIN : $SERVER_ADMIN</li>"
echo "<li>SERVER_NAME : $SERVER_NAME</li>"
echo "<li>SERVER_ADDR : $SERVER_ADDR</li>"
echo "<li>SERVER_PORT : $SERVER_PORT</li>"
echo "<li>SERVER_PROTOCOL : $SERVER_PROTOCOL</li>"
echo "<li>SERVER_SOFTWARE : $SERVER_SOFTWARE</li>"
echo "</ul>"
echo "<h2>Date & hour</h2>"
echo "<ul>"
echo "<li>TIME_YEAR : $TIME_YEAR</li>"
echo "<li>TIME_MON : $TIME_MON</li>"
echo "<li>TIME_DAY : $TIME_DAY</li>"
echo "<li>TIME_HOUR : $TIME_HOUR</li>"
echo "<li>TIME_MIN : $TIME_MIN</li>"
echo "<li>TIME_SEC : $TIME_SEC</li>"
echo "<li>TIME_WDAY : $TIME_WDAY</li>"
echo "<li>TIME : $TIME</li>"
echo "</ul>"
echo "<h2>Special variables</h2>"
echo "<ul>"
echo "<li>API_VERSION : $API_VERSION</li>"
echo "<li>THE_REQUEST : $THE_REQUEST</li>"
echo "<li>REQUEST_URI : $REQUEST_URI</li>"
echo "<li>REQUEST_FILENAME : $REQUEST_FILENAME</li>"
echo "<li>IS_SUBREQ : $IS_SUBREQ</li>"
echo "<li>HTTPS : $HTTPS</li>"
echo "<li>REQUEST_SCHEME : $REQUEST_SCHEME</li>"
echo "</ul>"
exit 0
# Endfile
