<?php

require_once \BASE_PATH . "/public/parts/header.php";
?>
<div>
    <h2>Welcome to food preference app.</h2>
    <h3>Search users by name</h3>
    <input type="text" id="searchInput" placeholder="Ex. John">
    <ol id="displayUsers"></ol>
</div>

<script>
    let UsersDiv = document.getElementById('displayUsers');
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchValue = this.value.trim();
        if(searchValue.length > 0){
      
        fetch(`/api/user/search?searchTerm=${searchValue}`)
                .then(response => response.json())
                .then(response => {
                    console.log(response);
                    if(response.status == 200){
                        displayUsers(response.data, UsersDiv);
                    }

                })
                .catch(error => console.error('Error:', error));
        }
    
    });

    function displayUsers(users,UsersDiv) {
        UsersDiv.innerHTML = '';
        users.forEach(user => {
            let li = document.createElement('li');
            li.textContent = `ID: ${user.id}, Name: ${user.name}, Lastname: ${user.lastname}, Email: ${user.email}, Created At: ${user.createdAt.date}`;
            UsersDiv.appendChild(li);
        });
    }
</script>

<?php
require \BASE_PATH . "/public/parts/footer.php";
?>