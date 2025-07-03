export default class WACCompare{
    constructor(){
        this.status = 0; // 0 = hidden, 1 = shown
        this.viewing = 0; // 0 = original, 1 = converted
        this.container = null;
        this.render();
        this.getElements();
        this.addListeners();
    }
    render(){
        this.container = document.createElement('div');
        this.container.id = 'compare-container';
        this.container.className = 'transparent float-container full-vw full-vh';
        this.container.innerHTML = `<div id="compare-header">
                <div id="compare-close" class="icon-wrapper"><div class="icon cross"></div></div>
                <div id="compare-next" class="icon-wrapper"><div class="icon arrow-right"></div></div>
            </div>
            <div id="compare-body">
                <div id="compare-original" class="compare-wrapper"><div class="compare-wrapper-title">Original</div><div class="compare-media-wrapper"></div></div>
                <div id="compare-converted" class="compare-wrapper"><div class="compare-wrapper-title">Converted</div><div class="compare-media-wrapper"></div></div>
            </div>`;
    }
    getElements(){
        this.close = this.container.querySelector('#compare-close');
        this.next = this.container.querySelector('#compare-next');
        this.original = this.container.querySelector('#compare-original .compare-media-wrapper');
        this.converted = this.container.querySelector('#compare-converted .compare-media-wrapper');
    }
    addListeners(){
        this.close.addEventListener('click', (e) => {
            this.hide();
        });
        this.next.addEventListener('click', (e) => {
            this.change();
        });
    }
    
    show(src_original, src_converted, media_type){
        if(this.status === 1) return;
        this.status = 1;
        this.viewing = 0;
        this.container.setAttribute('data-viewing', this.viewing);
        // this.container.classList.add('shown');
        this.container.classList.remove('transparent');
        if(media_type === 'image') {
            this.setImage(src_original, src_converted);
        } else if(media_type === 'video') {
            this.setVideo(src_original, src_converted);
        }
    }
    hide(){
        console.log('hide');
        this.status = 0;
        // this.container.classList.remove('shown');
        this.container.classList.add('transparent');
        this.original.innerHTML = '';
        this.converted.innerHTML = '';
    }
    // setMedia(src_original, media_type){
    //     const ext = media_type === 'image' ? 'webp' : 'webm';
    //     const src_converted = src_original.replace(/media\/(\d+)\.(\w+)/, 'media/$1.' + ext);
        
    // }
    change(){
        this.viewing = this.viewing === 0 ? 1 : 0;
        this.container.setAttribute('data-viewing', this.viewing);
    }
    setImage(src_original, src_converted){
        const original = document.createElement('img');
        original.src = src_original;
        const converted = document.createElement('img');
        converted.src = src_converted;
        this.original.innerHTML = '';
        this.original.appendChild(original);
        this.converted.innerHTML = '';
        this.converted.appendChild(converted);
    }
    setVideo(src_original, src_converted){

    }
    getPaddedId(id){
        let output = id.toString();
        while(output.length < 5) {
            output = '0' + output;
        }
        return output;
    }
    addTo(parent){
        parent.appendChild(this.container);
    }
}