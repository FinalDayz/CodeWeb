M.AutoInit();

$('[data-copy-target]').on('click', function () {
    const element = $(this);
    const targetSelector = element.data('copyTarget');
    const target = $(targetSelector);
    const textToCopy = target.text() || target.val();

    const inputElement = document.createElement('textarea');
    document.body.appendChild(inputElement);

    inputElement.value = textToCopy;
    inputElement.select();
    inputElement.setSelectionRange(0, textToCopy.length);

    document.execCommand("copy");

    inputElement.remove();

    if(element.data('tooltip')) {
        $('.tooltip-content').html('Text copied');
    }
});