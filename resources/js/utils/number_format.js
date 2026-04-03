export function number_format(user_input) {
    var filtered_number = user_input.replace(/[^0-9]/gi, '')
    var length = filtered_number.length
    var breakpoint = 1
    var formated_number = ''

    for (let i = 1; i <= length; i++) {
        if (breakpoint > 3) {
        breakpoint = 1
        formated_number = ',' + formated_number
        }
        formated_number =
        filtered_number.substring(length - i, length - (i - 1)) + formated_number

        breakpoint++
    }
    return formated_number
}
