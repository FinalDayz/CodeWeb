M.AutoInit();

$('[data-copy-target]').on('click', function () {
    const element = $(this);
    const targetSelector = element.data('copyTarget');
    const target = $(targetSelector);
    const textToCopy = target.text() || target.val();

    copyText(textToCopy);

    if(element.data('tooltip')) {
        $('.tooltip-content').html('Text copied');
    }
});

function copyText(textToCopy) {
    const inputElement = document.createElement('textarea');
    document.body.appendChild(inputElement);

    inputElement.value = textToCopy;
    inputElement.select();
    inputElement.setSelectionRange(0, textToCopy.length);

    document.execCommand("copy");

    inputElement.remove();
}

$('[data-share-or-copy]').on('click', function () {
    const element = $(this);
    const url = element.data('shareOrCopy');
    if(navigator.share) {
        navigator.share({
            title: element.data('shareTitle'),
            url: url
        });
    } else {
        copyText(url);
        if(element.data('tooltip')) {
            $('.tooltip-content').html('Text copied');
        }
    }
});
