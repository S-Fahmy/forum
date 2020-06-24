<html>

<head>


</head>

<body>
    <link rel="stylesheet" href="js/tribute.css" />

    <script src="/js/tribute.js">
        // import Tribute from "../laravel/node_modules/tributejs";
    // // window.Tribute = require('tributejs');


    var tribute = new Tribute({
    values: [
          { key: "Phil Heartman", value: "pheartman" },
          { key: "Gordon Ramsey", value: "gramsey" }
]
});

tribute.attach(document.getElementById("caaanDo"));

// also works with NodeList
tribute.attach(document.querySelectorAll(".mentionable"));

    </script>


    <div id="caaanDo">I'm Mr. Meeseeks, look at me!</div>

    <div class="mentionable">Some text here.</div>
    <div class="mentionable">Some more text over here.</div>




</body>

</html>