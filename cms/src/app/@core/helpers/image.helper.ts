export class ImageHelper {
  /*static fileToDataURL(file) {
    return new Promise((resolve) => {
      if (window.File && window.FileReader && window.FileList && window.Blob) {
        const fileType = file.type;
        const reader = new FileReader();
        reader.onloadend = function () {
          const tempImg: any = new Image();
          tempImg.src = reader.result;
          tempImg.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = tempImg.width;
            canvas.height = tempImg.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(this, 0, 0);
            const dataURL = canvas.toDataURL(fileType);
            resolve(dataURL);
          };
        };
        reader.readAsDataURL(file);
      } else {
        resolve(false);
      }
    });
  }*/

  static fileToDataURL(file) {
    return new Promise((resolve) => {
      const reader = new FileReader();
      reader.addEventListener('load', (event: Event) => {
        resolve((<any>event.target).result);
      }, false);
      reader.readAsDataURL(file);
    });
  }

  /***
   * Get File Url
   *
   * @param file
   * @param cb
   */
  static readURL(file: File, cb: Function): void {
    const reader = new FileReader();
    reader.onload = function (e: any) {
      cb(e.target.result);
    };
    reader.readAsDataURL(file);
  }

  static dataURItoBlob(dataURI): Blob {
    // convert base64/URLEncoded data component to raw binary data held in a string
    let byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0) {
      byteString = atob(dataURI.split(',')[1]);
    } else {
      byteString = unescape(dataURI.split(',')[1]);
    }
    // separate out the mime component
    const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
    // write the bytes of the string to a typed array
    const ia = new Uint8Array(byteString.length);
    for (let i = 0; i < byteString.length; i++) {
      ia[i] = byteString.charCodeAt(i);
    }
    return new Blob([ia], {type: mimeString});
  }

  static resizeImage(file: File, MAX_SIZE?: number) {
    return new Promise((resolve) => {
      resolve(file);
      /*MAX_SIZE = MAX_SIZE || 1920;
      const filename = file.name;
      if (!filename.match(/.(jpg|jpeg|png)$/i)) {
        resolve(file);
      } else {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
          const fileType = file.type;
          const reader = new FileReader();
          reader.onloadend = function() {
            const tempImg: any = new Image();
            tempImg.src = reader.result;
            tempImg.onload = function() {
              let w = tempImg.width;
              let h = tempImg.height;
              if (w > h) {
                if (w > MAX_SIZE) {
                  h *= MAX_SIZE / w;
                  w = MAX_SIZE;
                }
              } else {
                if (h > MAX_SIZE) {
                  w *= MAX_SIZE / h;
                  h = MAX_SIZE;
                }
              }
              const canvas = document.createElement('canvas');
              canvas.width = w;
              canvas.height = h;
              const ctx = canvas.getContext('2d');
              ctx.drawImage(this, 0, 0, w, h);
              const dataURL = canvas.toDataURL(fileType);
              const f: any = ImageHelper.dataURItoBlob(dataURL);
              f.name = filename;
              resolve(f);
            };
          };
          reader.readAsDataURL(file);
        } else {
          resolve(file);
        }
      }*/
    });
  }

  static resizeImages(files: any, MAX_SIZE?: number) {
    return new Promise((resolve) => {
      const promises = [];
      _.forEach(files, (file) => promises.push(ImageHelper.resizeImage(file, MAX_SIZE)));
      Promise.all(promises).then((data) => resolve(data), () => resolve([]));
    });
  }
}
