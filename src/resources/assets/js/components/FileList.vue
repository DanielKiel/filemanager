<template>
    <div>

    <md-button class="md-raised md-primary" @click="showDialog = ! showDialog">Upload</md-button>
    <md-button class="md-raised md-primary" @click="fetchFiles()">Lade alle Dateien</md-button>
    <md-button class="md-raised md-primary" @click="reset()">X</md-button>
    <md-field>
        <label>Suche</label>
        <md-input v-model="search"></md-input>
    </md-field>
    <div class="row row-eq-height">
        <div v-if="search !== undefined && searchedFiles.length > 0 " v-for="file in searchedFiles" :key="file.id" class="col-md-12 margin-bottom">
            <md-checkbox :key="file.id" :model="checked" :value="file.id">{{file.name}} / ({{file.created_at}}) </md-checkbox>
        </div>
        <div v-else v-for="file in existingFiles" :key="file.id" class="col-md-12 margin-bottom">
            <md-checkbox :key="file.id" :model="checked" :value="file.id">{{file.name}} / ({{file.created_at}}) </md-checkbox>
        </div>
    </div>

    <md-dialog :md-active.sync="showDialog">
          <md-dialog-title>FileUpload</md-dialog-title>
          <dion-fileupload :api="upload_api"></dion-fileupload>
    </md-dialog>
    </div>
</template>

<script>
    export default {
        props: [
            'el',
            'api',
            'upload_api'
        ],
        data() {
            return {
                existingFiles: [],
                searchedFiles: [],
                checked: [],
                showDialog: false,
                search: null
            }
        },

        created() {
            Bus.$on('file-uploaded', (file) => {
                this.existingFiles.push(file)
            })
        },

        watch: {
            checked: function(newVal, oldVal) {
                Bus.$emit('file-checked', newVal)
            },
            search: function(newVal, oldVal) {
                if (newVal === undefined) {
                    return
                }

                this.searchedFiles = this.existingFiles.filter( file => {
                    return file.name.toLowerCase().includes(this.search.toLowerCase())
                } )
            }
        },

        methods: {
            fetchFiles() {
                axios({
                    method: 'GET',
                    url: this.api
                }).then( response => {
                    this.search = ''
                    response.data.data.forEach( data => {
                        this.existingFiles.push(data)
                    } )

                } ).catch( err => {
                    console.log(err)
                } )
            },

            reset() {
                this.existingFiles = new Array
                this.searchedFiles = new Array
                this.search = ''
            }
        }
    }
</script>

<style>

.row-eq-height {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display:         flex;
  flex-wrap: wrap;
}
.row-eq-height > [class*='col-'] {
  display: flex;
  flex-direction: column;
}

.margin-bottom {
    margin-bottom: 15px;
}
</style>