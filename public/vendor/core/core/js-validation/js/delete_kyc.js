document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            let url = this.getAttribute("data-url");
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

            if (confirm("Are you sure you want to delete this form?")) {
                fetch(url, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": token,
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Form deleted successfully!");
                        location.reload();
                    } else {
                        alert("Error deleting form!");
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        });
    });
});