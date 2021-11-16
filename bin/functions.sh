PROJECT_ROOT="$(cd $DIR/../ && pwd)"


function import_env_file() {
    local file="$PROJECT_ROOT/$1"

    if [[ -f $file ]]; then
        set -o allexport
        source "$file"
        set +o allexport
    fi
}

function read_env_files() {
    local files=(
        .env
        .env.local
    )

    for file in "${files[@]}"; do
        import_env_file "$file"
    done

    for file in "${@}"; do
        import_env_file "$file"
    done
}


function prepareDebugging() {
    local enable=0
    local debug=0

    if [[ $1 == "debug" || $1 == "--debug" || $1 == "-dbg" ]]; then
        shift
        debug=1
    elif [[ $XDEBUG == 1 ]]; then
        debug=1
    fi

    if [[ $debug == 1 ]]; then
        enable=1
        DEBUG=("--env" "XDEBUG_MODE=debug")
    fi

    if [[ $1 == "profile" || $1 == "--profile" || $1 == "-prf" ]]; then
        shift
        enable=1
        DEBUG=("--env" "XDEBUG_MODE=profile")

        if [[ ! -d $PROJECT_ROOT/var/profiler ]]; then
            mkdir $PROJECT_ROOT/var/profiler
        fi
    fi

    if [[ $enable == 1 ]]; then
        DEBUG+=("--env" "XDEBUG_TRIGGER=1")

        if [[ -n $XDEBUG_CLIENT_HOST ]]; then
            DEBUG+=("--env" "XDEBUG_CLIENT_HOST=$XDEBUG_CLIENT_HOST")
            DEBUG+=("--env" "REMOTE_ADDR=$XDEBUG_CLIENT_HOST")
        fi
    fi

    ARGS=("${@}")
}
