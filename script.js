


document.addEventListener("DOMContentLoaded", function handleLikes() {
    const likeButtons = document.querySelectorAll(".like-btn");

    likeButtons.forEach(button => {
        button.addEventListener("click", function() {
            const articleId = this.getAttribute("data-article-id");
            const isLiked = this.getAttribute("data-liked") === "true";
            const action = isLiked ? "unlike" : "like";
            const button = this;

            fetch('like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    article_id: articleId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (action === "like") {
                        button.classList.add("liked");
                        button.setAttribute("data-liked", "true");
                        button.textContent = "Lubisz to";
                    } else {
                        button.classList.remove("liked");
                        button.setAttribute("data-liked", "false");
                        button.textContent = "Lubię to";
                    }
                }
            });
        });
    });
});
