document.addEventListener("DOMContentLoaded", function () {
    document
        .getElementById("loginForm")
        .addEventListener("submit", async function (e) {
            e.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            const data = {
                email: email,
                password: password,
            };

            const response = await fetch("/api/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (response.ok) {
                localStorage.setItem("token", result.token);
                window.location.href = "/";
            } else {
                alert(result.message || "Login failed!");
            }
        });
});
