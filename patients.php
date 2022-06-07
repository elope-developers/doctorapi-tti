<html>
<head>
    <link rel="stylesheet" href="https://unpkg.com/sakura.css/css/sakura.css" type="text/css">
</head>
<body>
    <script>
        // Grab the user's jwt from super secure cookies
        var jwt = "<?php echo (isset($_COOKIE["jwt"])) ? $_COOKIE["jwt"] : "badtoken"; ?>"

        // This function gets the logged in user's patients by passing the jwt and get_patients method to the api
        function getPatients() {
            fetch("api/fetch.php", {
                    method: 'POST',
                    body: JSON.stringify({
                        "token": jwt,
                        "method": "get_patients",
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then((res) => {
                    if (res.ok) {
                        return res.json()
                    } else {
                        throw 'error with response'
                    };
                })
                .then((data) => {
                    // if the api respods with a 200 status code, everything went fine
                    if (data.status == 200) {
                        var patients = data.api_response;
                        var elements = "";
                        patients.forEach(patient => {
                            elements += `<p>Email: ${patient.email}</p>` +
                                `<p>User ID: ${patient.user_id}</p>` +
                                `----</br>`;
                        });
                        document.getElementById("patients").innerHTML = elements;
                    } else {
                        alert(data.message);
                        document.getElementById("patients").innerHTML = data.message;
                    }
                })
                .catch((error) => {
                    alert("check console for error");
                    console.log(error)
                });
        }
    </script>


    <div id="patients"></div>
    <button onclick="getPatients()">GET PATIENTS</button>
</body>

</html>