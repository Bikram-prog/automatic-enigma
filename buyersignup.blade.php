@include('header')

        <div class="register-area ptb-100">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 col-12 col-lg-12 col-xl-6 ml-auto mr-auto">
                        <div class="login">
                            <div class="login-form-container">
                                <div class="login-form">
                                    <form action="/buyersignupaction" method="post">
                                       {{ csrf_field() }}
                                        @if (Session::has('errrmessage'))
                                                <div>{{Session::get('errrmessage')}}</div>
                                          @endif
                                        <input type="text" name="name" placeholder="Enter Your Name">
                                        <input name="email" placeholder="Enter Your Email" type="email">
                                        <input type="password" name="password" placeholder="Enter Your Password">
                                        <input type="password" name="repassword" placeholder="Re-enter Your Password">
                                         <div class="login-toggle-btn">
                                         	<label>Please Select Your Gender</label><br><br>
                                                <input type="checkbox" name="gender" value="male">
                                                <label>Male</label><br>
                                                <input type="checkbox" name="gender" value="fe-male">
                                                <label>Fe-male</label>
                                                
                                            </div>
										
                                        <div class="button-box">
                                            <button type="submit" class="default-btn floatright">Register</button>
                                        </div>
                                    </form>
                                    <center><a href="/buyerlogin">ALREADY A CUSTOMER?PLEASE LOGIN.</a></center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        		
@include('footer')