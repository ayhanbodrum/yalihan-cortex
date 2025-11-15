#!/bin/bash
# Logger Library for Scripts
# Usage: source scripts/lib/common.sh

# Log levels
LOG_LEVEL_DEBUG=0
LOG_LEVEL_INFO=1
LOG_LEVEL_WARN=2
LOG_LEVEL_ERROR=3

# Default log level
LOG_LEVEL=${LOG_LEVEL:-$LOG_LEVEL_INFO}

# Log directory
LOG_DIR="${LOG_DIR:-storage/logs/scripts}"

# Ensure log directory exists
mkdir -p "$LOG_DIR"

# Get log file name
get_log_file() {
    local script_name=$(basename "${BASH_SOURCE[2]}" .sh)
    local date=$(date +%Y%m%d)
    echo "$LOG_DIR/${script_name}-${date}.log"
}

# Log message
log() {
    local level=$1
    shift
    local message="$@"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    local log_file=$(get_log_file)
    
    # Check log level
    if [ $level -lt $LOG_LEVEL ]; then
        return 0
    fi
    
    # Level name
    case $level in
        $LOG_LEVEL_DEBUG) level_name="DEBUG" ;;
        $LOG_LEVEL_INFO) level_name="INFO" ;;
        $LOG_LEVEL_WARN) level_name="WARN" ;;
        $LOG_LEVEL_ERROR) level_name="ERROR" ;;
        *) level_name="UNKNOWN" ;;
    esac
    
    # Log to file
    echo "[$timestamp] [$level_name] $message" >> "$log_file"
    
    # Also print to console for ERROR and WARN
    if [ $level -ge $LOG_LEVEL_WARN ]; then
        case $level in
            $LOG_LEVEL_WARN) echo -e "\033[1;33m[$level_name]\033[0m $message" >&2 ;;
            $LOG_LEVEL_ERROR) echo -e "\033[0;31m[$level_name]\033[0m $message" >&2 ;;
        esac
    fi
}

# Log debug
log_debug() {
    log $LOG_LEVEL_DEBUG "$@"
}

# Log info
log_info() {
    log $LOG_LEVEL_INFO "$@"
}

# Log warning
log_warn() {
    log $LOG_LEVEL_WARN "$@"
}

# Log error
log_error() {
    log $LOG_LEVEL_ERROR "$@"
}

# Log script start
log_script_start() {
    local script_name=$(basename "${BASH_SOURCE[1]}")
    log_info "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    log_info "Script başladı: $script_name"
    log_info "Kullanıcı: $(whoami)"
    log_info "Dizin: $(pwd)"
    log_info "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
}

# Log script end
log_script_end() {
    local script_name=$(basename "${BASH_SOURCE[1]}")
    local exit_code=$?
    
    if [ $exit_code -eq 0 ]; then
        log_info "Script başarıyla tamamlandı: $script_name"
    else
        log_error "Script hata ile sonlandı: $script_name (exit code: $exit_code)"
    fi
    
    log_info "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
}

# Setup logging trap
setup_logging_trap() {
    trap 'log_script_end' EXIT
    log_script_start
}

