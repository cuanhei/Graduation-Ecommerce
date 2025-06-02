function updatePercent(percentDivID,percentage) {
    document.getElementById(percentDivID).style.background = `conic-gradient(
                rgb(0, 173, 0) 0% ${percentage}%,
                whitesmoke ${percentage}% 100%)`;
}

function confirmRemove() {
    return confirm("Are you sure you want to remove this Product from your cart?");
}
