<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js"></script>
    <script>
        var dropzone = new Dropzone('#file-upload', {
            previewTemplate: document.querySelector('#preview-template').innerHTML,
            parallelUploads: 3,
            thumbnailHeight: 150,
            thumbnailWidth: 150,
            maxFilesize: 5,
            filesizeBase: 1500,
            thumbnail: function (file, dataUrl) {
                if (file.previewElement) {
                    file.previewElement.classList.remove("dz-file-preview");
                    var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                    for (var i = 0; i < images.length; i++) {
                        var thumbnailElement = images[i];
                        thumbnailElement.alt = file.name;
                        thumbnailElement.src = dataUrl;
                    }
                    setTimeout(function () {
                        file.previewElement.classList.add("dz-image-preview");
                    }, 1);
                }
            }
        });

        var minSteps = 6,
            maxSteps = 60,
            timeBetweenSteps = 100,
            bytesPerStep = 100000;
        dropzone.uploadFiles = function (files) {
            var self = this;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));
                for (var step = 0; step < totalSteps; step++) {
                    var duration = timeBetweenSteps * (step + 1);
                    setTimeout(function (file, totalSteps, step) {
                        return function () {
                            file.upload = {
                                progress: 100 * (step + 1) / totalSteps,
                                total: file.size,
                                bytesSent: (step + 1) * file.size / totalSteps
                            };
                            self.emit('uploadprogress', file, file.upload.progress, file.upload
                                .bytesSent);
                            if (file.upload.progress == 100) {
                                file.status = Dropzone.SUCCESS;
                                self.emit("success", file, 'success', null);
                                self.emit("complete", file);
                                self.processQueue();
                            }
                        };
                    }(file, totalSteps, step), duration);
                }
            }
        }
    </script>
    <style>
        .dropzone {
            background: #e3e6ff;
            border-radius: 13px;
            max-width: 550px;
            margin-left: auto;
            margin-right: auto;
            border: 2px dotted #1833FF;
            margin-top: 0px;
        }
    </style>

<h1>Index</h1>
@foreach($users as $user)
	<div>{{$user->name}}</div>
@endforeach

<select id="userdata">
    <option value="" >
        Select a candidate
    </option>
    @foreach($users as $user)
        <option value="{{ $user->id }}" class="select_row">
            {{ $user->name }}
        </option>
    @endforeach
</select>
<br>

<form method="POST" action= "{{ route('update')}}" enctype="multipart/form-data" style="display: none;">
    @csrf
    <div class="form-data">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="username" name="username" placeholder="..." >
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="text" id="userEmail" name="userEmail" placeholder="..." >
        </div>
        <div class="field_wrapper ">
            <div>
                <input type="file" name="image[]" value=""/>
                <a href="javascript:void(0);" class="add_button" title="Add field"> Add</a>
            </div>
        </div>
        <div id="imageContainer">
            <!-- <a href="#">Deletevvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv</a> -->
        </div>

        <input type="hidden" id="id" name="id">
        <button type="submit">Update</button>
    </div>
</form>

<!-- --------------------------------------------------------------------------------- -->
<div id="dropzone">
    <form action="{{ route('FileUpload') }}" class="dropzone" id="file-upload" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="dz-message">
            Drag and Drop Single/Multiple Files Here<br>
        </div>
    </form>
</div>
<!-- --------------------------------------------------------------------------------- -->
<!-- <script>
    var getDataRoute = "{{ route('get.data', ['id' => ':id']) }}";
    var deleteImageRoute = "{{ route('image.delete', ['imageId' => ':imageId']) }}";
</script> -->

<script>
    $(document).ready(function(){
        $('#userdata').change(function(){
            var id = $(this).val();
            if(id){
                $.ajax({
                    url: "{{ route('get.data', ['id' => ':id']) }}".replace(':id', id),
                    type: 'GET',
                    dataType: "json",
                    success: function(response){
                        var useremail = response.useremail;
                        var username = response.username;
                        var id = response.id;
                        var images = response.images;
                        var imageid = response.imageid;
                        $('#userEmail').val(useremail);
                        $('#username').val(username);
                        $('#id').val(id);
                        $('#imageContainer').empty();
                        if (images.length > 0) {
                            for (var i = 0; i < images.length; i++) {
                                var imageUrl = images[i];
                                var imageId = imageid[i];

                                var $image = $('<img src="' + imageUrl + '" alt="User Image" style="height: 100px; width: 100px;">');
                                var $deleteButton = $('<button class="delete-btn" data-image-id="' + imageId + '">Delete</button>');
                                var $imageContainer = $('<div class="image-item"></div>');

                                $deleteButton.click(function() {
                                    var imageId = $(this).data('image-id');
                                    deleteImage(imageId); // Function to send AJAX request to delete image
                                });

                                $imageContainer.append($image);
                                $imageContainer.append($deleteButton);
                                $('#imageContainer').append($imageContainer);
                            }
                        }

                        $("form").show();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                    },
                });
            } else {
                $('#userEmail').val('');
                $("form").hide();
            }
        });
    });


// ----------------------------------------------------------------
    function deleteImage(imageId) {
        $.ajax({
            url: "{{ route('image.delete', ['imageId' => ':imageId']) }}".replace(':imageId', imageId),
            type: 'GET',
            data: { id: imageId },
            dataType: "json",
            success: function(response) {
                console.log('Image deleted successfully');
            },
            error: function(xhr, status, error) {
                console.log("Error:", error);
            }
        });
        window.alert(imageId);
    }




// ----------------------------------------------------------------
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var fieldHTML = '<div><input type="file" name="image[]" value=""/><a href="javascript:void(0);" class="remove_button"> Remove</a></div>'; //New input field html 
    var x = 1; //Initial field counter is 1
    
    // Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
        if(x < maxField){ 
            x++; //Increase field counter
            $(wrapper).append(fieldHTML); //Add field html
        }else{
            alert('A maximum of '+maxField+' fields are allowed to be added. ');
        }
    });
    
    // Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrease field counter
    });
</script>


<!-- <div class="field_wrapper ">
                            <div>
                                <input type="file" name="image[]" value=""/>
                                <a href="javascript:void(0);" class="add_button" title="Add field"> Add</a>
                            </div>
                        </div> -->

        <!-- <select name="data" id="data">
            <option value="" >
                Select a candidate
            </option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" class="select_row">
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <button type="submit">testing chal rhi hai!</button>
        <button >testing chal rhi hai!</button> -->