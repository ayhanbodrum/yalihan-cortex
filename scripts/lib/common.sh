#!/bin/bash
# Common Functions Library for Scripts
# Usage: source scripts/lib/common.sh

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Print colored message
color_print() {
    local color=$1
    shift
    local message="$@"
    
    case $color in
        red) echo -e "${RED}${message}${NC}" ;;
        green) echo -e "${GREEN}${message}${NC}" ;;
        yellow) echo -e "${YELLOW}${message}${NC}" ;;
        blue) echo -e "${BLUE}${message}${NC}" ;;
        magenta) echo -e "${MAGENTA}${message}${NC}" ;;
        cyan) echo -e "${CYAN}${message}${NC}" ;;
        *) echo "$message" ;;
    esac
}

# Print success message
print_success() {
    color_print "green" "✅ $@"
}

# Print error message
print_error() {
    color_print "red" "❌ $@"
}

# Print warning message
print_warning() {
    color_print "yellow" "⚠️  $@"
}

# Print info message
print_info() {
    color_print "blue" "ℹ️  $@"
}

# Check if command exists
check_dependency() {
    local cmd=$1
    if ! command -v "$cmd" &> /dev/null; then
        print_error "$cmd bulunamadı. Lütfen yükleyin."
        return 1
    fi
    return 0
}

# Check multiple dependencies
check_dependencies() {
    local missing=0
    for cmd in "$@"; do
        if ! check_dependency "$cmd"; then
            missing=$((missing + 1))
        fi
    done
    
    if [ $missing -gt 0 ]; then
        print_error "$missing bağımlılık eksik!"
        return 1
    fi
    return 0
}

# Validate input
validate_input() {
    local value=$1
    local description=$2
    
    if [ -z "$value" ]; then
        print_error "$description gerekli!"
        return 1
    fi
    return 0
}

# Validate file exists
validate_file() {
    local file=$1
    
    if [ ! -f "$file" ]; then
        print_error "Dosya bulunamadı: $file"
        return 1
    fi
    return 0
}

# Validate directory exists
validate_directory() {
    local dir=$1
    
    if [ ! -d "$dir" ]; then
        print_error "Dizin bulunamadı: $dir"
        return 1
    fi
    return 0
}

# Print header
print_header() {
    local title=$1
    local width=50
    local line=$(printf '━%.0s' $(seq 1 $width))
    
    echo ""
    color_print "blue" "$line"
    color_print "blue" "  $title"
    color_print "blue" "$line"
    echo ""
}

# Print footer
print_footer() {
    local message=$1
    local width=50
    local line=$(printf '━%.0s' $(seq 1 $width))
    
    echo ""
    color_print "green" "$line"
    color_print "green" "  $message"
    color_print "green" "$line"
    echo ""
}

# Ask for confirmation
ask_confirmation() {
    local message=$1
    local default=${2:-n}
    
    if [ "$default" = "y" ]; then
        local prompt="[Y/n]"
    else
        local prompt="[y/N]"
    fi
    
    read -p "$message $prompt: " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]] || ([ -z "$REPLY" ] && [ "$default" = "y" ]); then
        return 0
    else
        return 1
    fi
}

# Get script directory
get_script_dir() {
    local script_path="${BASH_SOURCE[0]}"
    if [ -L "$script_path" ]; then
        script_path=$(readlink "$script_path")
    fi
    dirname "$(cd "$(dirname "$script_path")" && pwd)"
}

# Get project root
get_project_root() {
    local current_dir=$(pwd)
    while [ "$current_dir" != "/" ]; do
        if [ -f "$current_dir/composer.json" ] || [ -f "$current_dir/package.json" ]; then
            echo "$current_dir"
            return 0
        fi
        current_dir=$(dirname "$current_dir")
    done
    return 1
}

# Exit with error
exit_with_error() {
    local message=$1
    local exit_code=${2:-1}
    
    print_error "$message"
    exit $exit_code
}

# Exit with success
exit_with_success() {
    local message=$1
    
    print_success "$message"
    exit 0
}

