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
# bin/build.sh : the CarteBlanche command line builder
#
# For more infos run:
# 
#     ~$ sh bin/build.sh -h
# 

######## Inclusion of the lib
LIBFILE="`dirname $0`/piwi-bash-library/piwi-bash-library.bash"
if [ -f "$LIBFILE" ]; then source "$LIBFILE"; else
    PADDER=$(printf '%0.1s' "#"{1..1000})
    printf "\n### %*.*s\n    %s\n    %s\n%*.*s\n\n" 0 $(($(tput cols)-4)) "ERROR! $PADDER" \
        "Unable to find required library file '$LIBFILE'!" \
        "Sent in '$0' line '${LINENO}' by '`whoami`' - pwd is '`pwd`'" \
        0 $(tput cols) "$PADDER";
    exit 1
fi
######## !Inclusion of the lib

#### SCRIPT SETTINGS #####################################################################

export PROJECT PROJECT_MANIFEST CONFIGFILE ARG_VAR ARG_VAL BUILD_DIR
declare -rax CONFIGENTRIES=(PHPBIN COMPOSERBIN SAMIPATH)
declare -rax PROJECTREQUIREDDIRS=('config' 'i18n' 'var')
declare -rax PROJECTWRITABLEDIRS=('www/tmp')

export CONFIGNAME='.build.conf'
export BUILDNAME='build/'
export SAMIBIN="src/vendor/sami/sami/sami.php"
export COMPOSER_DOWNLOAD='https://getcomposer.org/installer'
export CARTEBLANCHE_REPO='https://github.com/php-carteblanche/carteblanche'

export PHPBIN=$(which php)
export COMPOSERBIN=$(which composer)
if [ -z $COMPOSERBIN ]; then COMPOSERBIN='${BUILDNAME}composer.phar'; fi
export GITCMD=$(which git)
export SAMIPATH=''
export SETTING='no-dev'

MANPAGE_NODEPEDENCY=true
NAME="Carte Blanche builder"
VERSION="0.0.1-dev"
DESCRIPTION="This script is a helper to build, install or update a Carte Blanche package. By default any required binary will be installed in a '${BUILDNAME}' directory\n\
\tat the poject root but you can use options to define values to fit your system. Any value define in a '${CONFIGNAME}' file will over-write defaults.\n\
\n\
\t<bold><underline>Available actions:</underline></bold> \n\
\t<bold><${COLOR_INFO}>version</${COLOR_INFO}></bold>\t\tget current package informations \n\
\t<bold><${COLOR_INFO}>changelog</${COLOR_INFO}></bold>\tgit current package changelog history (if possible - requires 'git' binaries) \n\
\t<bold><${COLOR_INFO}>install</${COLOR_INFO}></bold>\t\twill launch 'composer install' ; use the 'set' option to define a specific install ; default set is '${SETTING}' \n\
\t<bold><${COLOR_INFO}>update</${COLOR_INFO}></bold>\t\twill launch 'composer update' ; use the 'set' option to define a specific install ; default set is '${SETTING}' \n\
\t<bold><${COLOR_INFO}>self-update</${COLOR_INFO}></bold>\twill try to update your CarteBlanche root installation (not the dependencies) to last update \n\
\t<bold><${COLOR_INFO}>reset</${COLOR_INFO}></bold>\t\twill clean all third-party files and refresh composer's cache \n\
\t<bold><${COLOR_INFO}>rebuild</${COLOR_INFO}></bold>\t\twill process actions 'reset' then 'install' \n\
\t<bold><${COLOR_INFO}>dump-autoload</${COLOR_INFO}></bold>\twill run command 'composer dump-autoload' and rebuild the package autoloaders files \n\
\t<bold><${COLOR_INFO}>update-modules</${COLOR_INFO}></bold>\twill update all project git-submodules (requires 'git' binaries) \n\
\t<bold><${COLOR_INFO}>full-update</${COLOR_INFO}></bold>\tmake a Composer and Git-submodules update \n\
\t<bold><${COLOR_INFO}>render-doc</${COLOR_INFO}></bold>\twill render the Sami documentation ; use the 'set' option to define a 'full' install ; default set is empty ; this requires the Sami dev package \n\
\t<bold><${COLOR_INFO}>update-doc</${COLOR_INFO}></bold>\twill update the Sami documentation ; use the 'set' option to define a 'full' install ; default set is empty ; this requires the Sami dev package \n\
\t<bold><${COLOR_INFO}>clean</${COLOR_INFO}></bold>\t\twill clean all temporary Carte Blanche files (cached files)\n\
\t<bold><${COLOR_INFO}>config</${COLOR_INFO}></bold>\t\twork with builder config: if a 'var' argument is set, read the value of the configuration entry ; if a 'var' & 'val' arguments are set, \n\
\t\t\tdefine the value of the configuration entry ; if no argument 'var', read the entire config ; default config file is '${CONFIGNAME}'\n\
\t<bold><${COLOR_INFO}>update-config</${COLOR_INFO}></bold>\tupdate builder configuration with defined binaries values (for Composer, PHP and Sami) \n\
\t<bold><${COLOR_INFO}>clear-config</${COLOR_INFO}></bold>\tclear current builder configuration";
SYNOPSIS="~\$ <bold>${0}</bold>  -[<underline>COMMON OPTIONS</underline>]  --[<underline>SCRIPT OPTIONS</underline>=<underline>VALUE</underline>]  <underline>ACTION</underline>  --";
OPTIONS="<bold>--set=SET</bold>\t\tSet an optional argument used for some actions \n\
\t<bold>--var=VARNAME</bold>\t\tSet a variable name (for config entries) \n\
\t<bold>--val=VALUE</bold>\t\tSet the value of the optional 'var' argument (for config entries) \n\
\t<bold>--php=PATH</bold>\t\tPath of the PHP binary to use (default is '${PHPBIN}') \n\
\t<bold>--composer=PATH</bold>\t\tPath of the Composer script to use (default is '${COMPOSERBIN}') \n\
\t<bold>--sami=PATH</bold>\t\tPath of the Sami binary to use (default is '${SAMIBIN}') \n\
\t<bold>--config=PATH</bold>\t\tPath of a builder configuration file \n\
\t${COMMON_OPTIONS_LIST}";

#### LIBRARY #############################################################################

#### requireGitCmd ()
requireGitCmd () {
    if [ -z $GITCMD ]; then
        commanderror 'git'
    fi
    return 0
}

#### requireComposerBin ()
requireComposerBin () {
    local COMPOSERBIN_TMP="${COMPOSERBIN##* }"
    if [ -z "$COMPOSERBIN" -o ! -f "$COMPOSERBIN_TMP" ]; then
        requireProjectBuild
        installComposer
        if [ ! -f "$COMPOSERBIN_TMP" ]; then
            patherror "$COMPOSERBIN_TMP"
        fi
    fi
    return 0
}

#### requireSamiBin ()
requireSamiBin () {
    if [ -z "$SAMIPATH" ]; then
        export SAMI="${PROJECT}/${SAMIBIN}"
        export SAMIPATH="${SAMI}"
        if [ ! -f $SAMI ]; then
            error "Sami is not installed yet, run '--set=dev install' first!"
            exit 0
        fi
    else
        export SAMI="${SAMIPATH}"
        if [ ! -f $SAMI ]; then
            patherror "$SAMI"
        fi
    fi
    return 0
}

#### requireProjectBuild ()
requireProjectBuild () {
    export BUILD_DIR="${PROJECT}/${BUILDNAME}"
    if [ ! -d $BUILD_DIR ]; then
        mkdir $BUILD_DIR && chmod 777 $BUILD_DIR
        if [ ! -d $BUILD_DIR ]; then
            error "The build temporary directory can not be created at the package root! Run the script as 'sudo' (trying to build '${BUILD_DIR}')"
        fi
    fi
    return 0
}

#### installComposer ()
installComposer () {
    local COMPOSERBIN_TMP="${COMPOSERBIN##* }"
    verecho "Installing 'composer.phar' in '${COMPOSERBIN_TMP}'"
    iexec "curl -sS ${COMPOSER_DOWNLOAD} | ${PHPBIN} -- --install-dir=${COMPOSERBIN_TMP}"
}

#### actionReset ()
actionReset () {
    verecho "Launching action 'reset'"
    iexec "rm -rf ${PROJECT}/phpdoc"
    iexec "rm -rf ${PROJECT}/src/vendor"
    iexec "rm -rf ${PROJECT}/src/bundles"
    iexec "rm -rf ${PROJECT}/src/tools"
    iexec "rm -rf ${PROJECT}/www/vendor"
    iexec "rm -rf ${PROJECT}/www/tmp"
    iexec "rm -rf ${PROJECT}/config/vendor"
    iexec "rm -f ${PROJECT}/config/bootstrap.php"
    iexec "rm -rf ${PROJECT}/i18n/vendor"
    iexec "rm -rf ${PROJECT}/var/i18n"
    iexec "rm -rf ${PROJECT}/var/logs"
    iexec "rm -f ${PROJECT}/composer.lock"
    iexec "find ${PROJECT}/bin -maxdepth 1 -type l -exec rm -f {} \;"
    iexec "find ${PROJECT}/doc -type l -exec rm -f {} \;"
}

#### actionClean ()
actionClean () {
    verecho "Launching action 'clean'"
    iexec "rm -rf ${PROJECT}/www/tmp/*"
    if [ ! -z $FORCED ]; then
        iexec "rm -rf ${PROJECT}/var/logs"
    fi
}

#### actionInstall ()
actionInstall () {
    verecho "Launching action 'composer install'"
    requireProjectBuild
    requireComposerBin
    for i in "${PROJECTREQUIREDDIRS[@]}"; do
        if [ ! -d $i ]; then
            mkdir $i && chmod 775 $i
        fi
    done
    if [ $VERBOSE ]; then
        iexec "${COMPOSERBIN} install -v --${SETTING}"
    else
        iexec "${COMPOSERBIN} install --${SETTING}"
    fi
    for i in "${PROJECTWRITABLEDIRS[@]}"; do
        if [ ! -d $i ]; then
            mkdir $i && chmod 775 $i
        fi
    done
}

#### actionUpdate ()
actionUpdate () {
    verecho "Launching action 'composer update'"
    requireProjectBuild
    requireComposerBin
    if [ $VERBOSE ]; then
        iexec "${COMPOSERBIN} update -v --${SETTING}"
    else
        iexec "${COMPOSERBIN} update --${SETTING}"
    fi
}

#### actionUpdateModules ()
actionUpdateModules () {
    verecho "Updating git submodules"
    requireGitCmd
    iexec "git submodule foreach git pull"
}

#### actionDumpAutoload ()
actionDumpAutoload () {
    verecho "Launching action 'dump-autoload'"
    requireProjectBuild
    requireComposerBin
    iexec "${COMPOSERBIN} dump-autoload"
}

#### actionDocRender ()
actionDocRender () {
    verecho "Launching action 'doc-render'"
    requireProjectBuild
    requireSamiBin
    if [ ! -z "$SETTING" -a "$SETTING" = 'full' ]; then
        iexec "${PHPBIN} ${SAMI} render etc/vendor/sami.config.full.php"
    else
        iexec "${PHPBIN} ${SAMI} render etc/vendor/sami.config.php"
    fi
}

#### actionDocUpdate ()
actionDocUpdate () {
    verecho "Launching action 'doc-update'"
    requireProjectBuild
    requireSamiBin
    if [ ! -z "$SETTING" -a "$SETTING" = 'full' ]; then
        iexec "${PHPBIN} ${SAMI} update etc/vendor/sami.config.full.php"
    else
        iexec "${PHPBIN} ${SAMI} update etc/vendor/sami.config.php"
    fi
}

#### actionVersion ()
actionVersion () {
    package_version=$(sed -n 's/.*"version"*: "*\([^ ]*.*\)",/\1/p' < $PROJECT_MANIFEST)
    if `isgitclone`
        then echo "Carte Blanche ${package_version} `git rev-parse HEAD`"
        else echo "Carte Blanche ${package_version}"
    fi
}

#### actionChangelog ()
actionChangelog () {
    requireGitCmd
    if `isgitclone`
        then iexec "git log"
        else echo "no changelog info (not a GIT clone) ..."
    fi
}

#### actionConfig ()
actionConfig () {
    if [ ! -z "$ARG_VAR" ]; then
        if ! `in_array "$ARG_VAR" "${CONFIGENTRIES[@]}"`; then
            error "Unknown configuration entry '$ARG_VAR'!"
        fi
        if [ ! -z "$ARG_VAL" ]; then
            setconfigval $CONFIGFILE "$ARG_VAR" "$ARG_VAL"
            quietecho "setting config: ${ARG_VAR}=`getconfigval $CONFIGFILE \"$ARG_VAR\"`"
        else
            echo "${ARG_VAR}=`getconfigval $CONFIGFILE \"$ARG_VAR\"`"
        fi
    else
        quietecho "reading config file: ${CONFIGFILE}"
        cat $CONFIGFILE
    fi
    return 0
}

#### actionUpdateConfig ()
actionUpdateConfig () {
    requireComposerBin
    requireSamiBin
    for key in "${CONFIGENTRIES[@]}"; do
        eval "value=\$$key"
        echo "$key : $value"
        setconfigval $CONFIGFILE "$key" "$value"
    done
    return 0
}

#### actionSelfUpdate ()
actionSelfUpdate () {
    requireGitCmd
    BRANCHNAME=`git rev-parse --abbrev-ref HEAD`
    SF_BRANCHNAME="selfupdate-`date \"+%s\"`"
    verecho "Self-updating CarteBlanche on branch $BRANCHNAME (passing by temporary branch $SF_BRANCHNAME)"
    TEMPCMD="git branch $SF_BRANCHNAME && \
        git checkout $SF_BRANCHNAME && \
        git add . && \
        git commit -m '$SF_BRANCHNAME' && \
        git checkout $BRANCHNAME && \
        git pull && \
        git merge $SF_BRANCHNAME && \
        git branch -D $SF_BRANCHNAME";
    if [ ! $VERBOSE ]; then
        TEMPCMD="$TEMPCMD > /dev/null"
    fi
    iexec "$TEMPCMD"
    verecho "OK - CarteBlanche is up-to-date on branch '$BRANCHNAME'"
    return 0
}

#### SCRIPT PROCESS ######################################################################
verecho "_ go"

## get current project path and check it exists
    export PROJECT="$WORKINGDIR"
    export PROJECT_MANIFEST="${PROJECT}/composer.json"
    if [ ! -f $PROJECT_MANIFEST ]; then
        error "The 'composer.json' file can't be found in ${PROJECT} - Please run this script from the root directory of the package."
    fi

## get the project config file path, check it exists and read it if so
    if [ -z $CONFIGFILE ]; then
        export CONFIGFILE="${PROJECT}/${CONFIGNAME}"
    fi
    if [ -f $CONFIGFILE ]; then readconfigfile "$CONFIGFILE"; fi

rearrangescriptoptions "$@"
[ "${#SCRIPT_OPTS[@]}" -gt 0 ] && set -- "${SCRIPT_OPTS[@]}";
[ "${#SCRIPT_ARGS[@]}" -gt 0 ] && set -- "${SCRIPT_ARGS[@]}";
[ "${#SCRIPT_OPTS[@]}" -gt 0 -a "${#SCRIPT_ARGS[@]}" -gt 0 ] && set -- "${SCRIPT_OPTS[@]}" -- "${SCRIPT_ARGS[@]}";
parsecommonoptions
if $DEBUG; then library_debug "$*"; fi

OPTIND=1
while getopts ":${OPTIONS_ALLOWED}" OPTION; do
    OPTARG="${OPTARG#=}"
    case $OPTION in
    # you must keep these ones
        h|f|i|q|v|x|V|d) ;;
        -) LONGOPTARG="`getlongoptionarg \"${OPTARG}\"`"
            case $OPTARG in
    # you must keep these ones
                help|man|usage|vers*|interactive|verbose|force|debug|dry-run|quiet|libvers) ;;
    # specifics
                set*) export SETTING="${LONGOPTARG}";;
                php*)  export PHPBIN="${LONGOPTARG}";;
                sami*)  export SAMIPATH="${LONGOPTARG}";;
                composer*) export COMPOSERBIN="${LONGOPTARG}";;
                config*) export CONFIGFILE="${LONGOPTARG}";;
                var*) export ARG_VAR=$(strtoupper "${LONGOPTARG}");;
                val*) export ARG_VAL="${LONGOPTARG}";;
                ?) error "Unknown option '$OPTARG'" 1;;
            esac ;;
        ?) error "Unknown option '$OPTION'" 1;;
    esac
done

getnextargument
ACTION="${ARGUMENT}"

if [ ! -z "$ACTION" ]; then

## self-update
    if [ "$ACTION" = 'self-update' ]; then actionSelfUpdate;

## reset
    elif [ "$ACTION" = 'reset' ]; then actionReset;

## clean
    elif [ "$ACTION" = 'clean' ]; then actionClean;

## install
    elif [ "$ACTION" = 'install' ]; then actionInstall;

## update
    elif [ "$ACTION" = 'update' ]; then actionUpdate;

## update-modules
    elif [ "$ACTION" = 'update-modules' -o "$ACTION" = 'modules-update' ]; then actionUpdateModules;

## dump-autoload
    elif [ "$ACTION" = 'dump-autoload' -o "$ACTION" = 'autoload-dump' -o "$ACTION" = 'dumpautoload' -o "$ACTION" = 'autoloaddump' ];
        then actionDumpAutoload;

## render-doc
    elif [ "$ACTION" = 'render-doc' -o "$ACTION" = 'doc-render' ]; then actionDocRender;

## update-doc
    elif [ "$ACTION" = 'update-doc' -o "$ACTION" = 'doc-update' ]; then actionDocUpdate;

## config
    elif [ "$ACTION" = 'config' -o "$ACTION" = 'configuration' ]; then actionConfig;

## update-config
    elif [ "$ACTION" = 'update-config' -o "$ACTION" = 'update-configuration' ]; then actionUpdateConfig;

## version
    elif [ "$ACTION" = 'version' -o "$ACTION" = 'vers' ]; then actionVersion;

## changelog
    elif [ "$ACTION" = 'changelog' -o "$ACTION" = 'log' ]; then actionChangelog;

## rebuild
    elif [ "$ACTION" = 'rebuild' ]; then
        echo "> resetting current installation ..."
        actionReset
        echo "> ok"
        actionInstall

## full-update
    elif [ "$ACTION" = 'full-update' ]; then
        verecho "> updating current installation ..."
        actionUpdate
        actionUpdateModules

## ?
    else error "Unknown action '$ACTION'!"; fi;

else title true; usage; fi;

verecho "_ ok"

#### END OF SCRIPT #######################################################################
exit 0

# Endfile
