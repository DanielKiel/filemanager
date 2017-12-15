<template>
    <form enctype="multipart/form-data">
        <div class="col-md-12">
             <md-card v-for="file in existingFiles" :key="file.id" class="col-md-12">

                <md-card-content>
                    Uploaded: {{file.name}}
                </md-card-content>

            </md-card>
            <form enctype="multipart/form-data">
                <md-subheader>Dateien hochladen</md-subheader>
                <md-field>
                  <label>Upload files</label>
                  <md-file id="uploadInput" @change="filesChange($event.target.files)" multiple placeholder="Upload files" />
                </md-field>
                <md-list v-if="files.length > 0" class="md-double-line">
                    <md-list-item v-for="(file, index) in files" :key="index">
                        <div class="md-list-text-container">
                            <span>{{file.name}}</span>
                            <md-progress-bar class="md-accent" md-mode="determinate" :md-value="progress[file.name]"></md-progress-bar>
                        </div>
                    </md-list-item>
                </md-list>
            </form>
        </div>
    </form>
</template>

<script>
    export default {
        props: [
            'el',
            'api',
            'path'
        ],
        data() {
            return {
                existingFiles: [],
                files: [],
                dir: this.path,
                formData: new FormData(),
                progress: {}
            }
        },

        created() {
            if (FileBus !== undefined) {
                FileBus.$on('path-changed', (obj) => {
                    this.dir = obj.path
                })
            }
        },

        methods: {
            save() {
                axios({
                    method: 'POST',
                    url: this.api,
                    data: this.formData
                }).then( response => {
                    let data = response.data
                    this.$set(this.progress, data.name, 100)

                    this.files.forEach( (file, index) => {
                        if (file.name === data.name) {
                            this.$delete(this.files, index)
                        }
                    } )

                    //next request to get source
                    this.existingFiles.push(data)

                    this.$emit('file:uploaded', data)
                    //TODO remove dependency to Bus, cause Bus may not exists !!!
                    Bus.$emit('file-uploaded', data)

                } ).catch( err => {
                    console.log(err)
                })
            },

            filesChange(fileList) {
                this.files = []

                // handle file changes
                if (!fileList.length) return

                // append the files to FormData
                Array
                  .from(Array(fileList.length).keys())
                  .map(x => {
                    this.files.push(fileList[x])
                    this.formData = new FormData()
                    this.progress[fileList[x].name] = 10
                    this.formData.append('upload', fileList[x], fileList[x].name)

                    if (this.dir !== undefined) {
                        this.formData.append('directory', this.dir)
                    }

                     // save it
                    this.save()
                  })
            }
        }
    }
</script>