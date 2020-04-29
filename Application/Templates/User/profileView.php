<script src="<?=BACKSTEP?><?=APPLICATION?>Templates/User/profile.js?v=<?= CURRENT_TIMESTAMP ?>"></script>
<div class="modal" id="usr-modal"></div>
<main class="main-body">    
    <section class="user-profile-structure"> 
        <p class="title">Drawer</p>       
        <div class="user-datas-tbl">
            <div class="model-content-container" id="usr-mdl-content">
                <form action="/user/update">                        
                    <table class="user-datas-table">
                        <caption>Yout personal datas:</caption>     
                        <tr>
                            <td>Name:</td>
                            <td><input class="persona-data-input" type="text" value="<?=$user->userName?>" readonly></td>
                        </tr>            
                        <tr>
                            <td>E-mail:</td>
                            <td><input class="persona-data-input" type="email" value="<?=$user->email?>" readonly></td>
                        </tr>            
                        <tr>
                            <td>Login date:</td>
                            <td><?=$user->loginDatetime?></td>
                        </tr>            
                        <tr>
                            <td>Birth:</td>
                            <td><input class="persona-data-input" type="date" value="<?=$user->birth?>" readonly></td>
                        <tr>
                            <td>Sex:</td>
                            <td><input class="persona-data-input" type="text" value="<?=$user->sex?>" readonly></td>
                        </tr>          
                        <tr>
                            <td>Password:</td>
                            <td><input class="persona-data-input" type="password" value="<?php for($i=0;$i<$user->passwordLength;$i++){echo '*';} ?>" readonly></td>
                        </tr> 
                        <tr>
                            <td><input type="reset" value="Reset" class="controlInputs"></td>
                            <td><input type="submit" value="Send" class="controlInputs"></td>
                        </tr>                               
                    </table>
                </form>
            </div>
        </div>    
        <div class="user-image" id="user-profile-image">
            <img class="big-user-image" id="output" src="data:image/*;base64,<?= $user->imgBin?>">              
        </div>  
        <div class="d">Change your privates datas whenever you want! <a href="<?=APPLICATION?>user/df" id="opn-usr-mdl"> Change</a></div>
        <div class="user-img-chng-cell">
            <form action="/user/update">                 
                <input id="update-img" type="file" name="create-user-file"  accept="image/*">         
                <label for="update-img" class="btn btn-gray " >Change</label>
            </form>
        </div>                  
        <div class="line"></div> 
    </section>
</main>        