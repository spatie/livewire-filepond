import * as FilePond from 'filepond';

import 'filepond/dist/filepond.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';

FilePond.registerPlugin(FilePondPluginImagePreview);
FilePond.registerPlugin(FilePondPluginFileValidateSize);
FilePond.registerPlugin(FilePondPluginFileValidateType);
FilePond.registerPlugin(FilePondPluginImageCrop);

const URLtoFile = async function (path) {
    let url = `${path}`;
    let name = url.split('/').pop();
    const response = await fetch(url);
    const data = await response.blob();
    const metadata = {
        name: name,
        path: path,
        size: data.size,
        type: data.type
    };
    let file = new File([data], path, metadata);

    return {
        source: file,
        options: {
            type: 'local',
            metadata,
        }
    }
}

window.LivewireFilePond = FilePond;
window.URLtoFile = URLtoFile;
