@extends('template')

@section('script')

      
       <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

      <!-- ajax visitorActivityData -->
      <script src="{{ asset('admin_lang/visitorActivityData/ajax-page-activity.js')}}"></script>

            <!-- ajax visitorActivityData -->
            <script src="{{ asset('admin_lang/visitorActivityData/ajax-button-activity.js')}}"></script>


            <script src="{{ asset('admin_lang/lang/javascript/add_company.js')}}"></script>
            <script src="{{ asset('admin_lang/lang/javascript/get_company.js')}}"></script>
       
       <script>
   
      </script>
@endsection
@section('contents')

  {{-- Page Activity  --}}
   <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
      <div class="card-body">
         <div class="d-flex justify-content-between">
            <h4 class="card-title">Company Names</h4>
            <div class="d-flex justify-content-End">
             <!-- Button trigger modal -->
                  
            <button type="button" class="btn" style="background-color:#212529;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">ADD</button>
                <!-- Modal -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Company</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                     <div class="mb-3">
                        <form  id="AddCompanyForm">
                           
                        <label for="exampleFormControlInput1" class="form-label m-2">Company Name</label>
                              <input type="text" class="form-control" id="CompanyName" placeholder="Please Enter Company Name">  
                              <label for="exampleFormControlInput1" class="form-label m-2">Office Number</label>
                              <input type="tel" class="form-control" id="CompanyContact" placeholder="Contact Number">
                             <label for="exampleFormControlInput1" class="form-label m-2">Address</label>
                             <input type="text" class="form-control" id="CompanyAddress" placeholder="Please Enter your Address">
                             <label for="exampleFormControlInput1" class="form-label m-2">Company Url</label>
                             <input type="url" class="form-control" id="CompanyUrl" placeholder="Please type company url">
                             <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                  </form>
                        
                     </div>
                    </div>
                    
                    </div>
                </div>
                </div>
            
            </div>






        </div>
         
         <div class="row">
 

         <div class="table-responsive">
            <table class="table table-dark">
            <thead>
               <tr>
                  <th> # </th>
                  <th>Company Name</th>
                  <th>Contact Number</th>
                  <th>Address</th>
                  <th>Company Link</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody class="activity-report-table">
                 
            </tbody>
            </table>
         </div>
      </div>
      </div>
   </div>


@endsection