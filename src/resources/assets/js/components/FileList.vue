<template>
    <div>

    <md-button class="md-icon-button md-raised md-primary" @click="showDialog = ! showDialog">
        <md-tooltip>Dateien hochladen</md-tooltip>
        <md-icon>file_upload</md-icon>
    </md-button>
    <md-button class="md-icon-button md-raised md-default" @click="fetchFiles(api)">
        <md-tooltip>Zeige alle hochgeladenen Dateie</md-tooltip>
        <md-icon>folder_open</md-icon>
    </md-button>
    <md-button v-if="existingFiles.length > 0" class="md-icon-button md-raised md-accent" @click="reset()">
        <md-tooltip>Liste von der Anzeige entfernen</md-tooltip>
        <md-icon>clear</md-icon>
    </md-button>
    <md-field v-if="existingFiles.length > 0">
        <label>Suche in Dateien nach Name</label>
        <md-input v-model="search"></md-input>
    </md-field>
    <div class="row row-eq-height">
        <div v-if="searched === false">
            <div v-for="file in existingFiles" :key="file.id" class="col-md-12 margin-bottom">
                <md-checkbox :key="file.id" :model="checked" :value="file.id">{{file.name}} / ({{file.created_at}}) </md-checkbox>
            </div>
        </div>
        <div v-else>
            <div v-for="file in searchedFiles" :key="file.id" class="col-md-12 margin-bottom">
                <md-checkbox :key="file.id" :model="checked" :value="file.id">{{file.name}} / ({{file.created_at}}) </md-checkbox>
            </div>
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
                search: null,
                searched: false
            }
        },

        created() {
            Bus.$on('file-uploaded', (file) => {
                this.existingFiles.push(file)
                this.checked.push(file.id)
            })
        },

        watch: {
            checked: function(newVal, oldVal) {
                Bus.$emit('file-checked', newVal)
            },
            search: function(newVal, oldVal) {
                if (newVal === undefined || newVal === null || newVal === '') {
                    this.searched = false
                    return
                }

                this.searched = true

                this.searchedFiles = this.existingFiles.filter( file => {
                    return file.name.toLowerCase().includes(this.search.toLowerCase())
                } )
            }
        },

        methods: {
            fetchFiles(url) {
                axios({
                    method: 'GET',
                    url: url
                }).then( response => {
                    this.search = ''
                    response.data.data.forEach( data => {
                        this.existingFiles.push(data)
                    } )

                    if (response.data.next_page_url !== null) {
                        this.fetchFiles(response.data.next_page_url )
                    }

                } ).catch( err => {
                    console.log(err, url)
                } )
            },

            reset() {
                this.existingFiles = new Array
                this.searchedFiles = new Array
                this.search = ''
                this.checked = new Array
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

.md-dialog {
    height: 100%;
    overflow-y: auto;

}
</style>