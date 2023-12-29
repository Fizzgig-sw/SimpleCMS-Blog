<script>
    function toggle(showHideDiv, switchTextDiv, text1, text2) {
        use1=text1;
        use2=text2;
        if(text1===undefined){use1="Show";}
        if(text2===undefined){use2="Hide";}

        var ele = document.getElementById(showHideDiv);
        var text = document.getElementById(switchTextDiv);
        if(ele.style.display == "block") {
                ele.style.display = "none";
            text.innerHTML = "<span style='font-size: 80%;'>" + use1 + "</span>";
        }
        else {
            ele.style.display = "block";
            text.innerHTML = "<span style='font-size: 80%;'>" + use2 + "</span>";
            }
    }

    function download(){
        var text = document.getElementById("content").value;
        text = text.replace(/\n/g, "\r\n"); // To retain the Line breaks.
        var blob = new Blob([text], { type: "text/plain"});
        var anchor = document.createElement("a");
        anchor.download = "BlogFile-" + document.getElementById("title").value + ".md";
        anchor.href = window.URL.createObjectURL(blob);
        anchor.target ="_blank";
        anchor.style.display = "none"; // just to be safe!
        document.body.appendChild(anchor);
        anchor.click();
        document.body.removeChild(anchor);
    }
    document.getElementById("cancel-submit").onkeypress = function(e) {
        var key = e.charCode || e.keyCode || 0;     
        if (key == 13) {
            e.preventDefault();
        }
    }
</script>