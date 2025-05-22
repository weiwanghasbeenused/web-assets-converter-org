export default class WACItem{
    constructor(item, imgFormats, vidFormats, onChange=null){
        this.imgFormats = imgFormats;
        this.vidFormats = vidFormats;
        this.ext = item.type;
        this.type = this.imgFormats.includes(this.ext) ? 'image' : (this.vidFormats.includes(this.ext) ? 'video' : '');
        this.dom = null;
        this.media_id = item.id;
        this.input = null;
        this.callback = onChange;
        this.checked = false;
        this.converted = item.converted == 1; // 1 or 0
        this.writable = item.writable;
        if (!this.type) return null;
        this.render(item);
        this.getElements();
        this.addListeners();
    }
    render(item) {
        const id = item.id;
        const e_id = `media-${id}`;
        const spec = item.spec ? item.spec.split('x').map(Number) : [1, 1];
        const proportion = Math.round((spec[1] * 100 / spec[0])) / 100;
        const converted = this.converted ? '1' : '0';
        const filename = this.getFilename(id, this.ext);
        const src = `${window.location.origin}/media/${filename}`;
        
        const media = this.renderMedia(src, proportion, converted);
        this.dom = document.createElement('li');
        this.dom.className = 'list-item';
        this.dom.setAttribute('data-id', id);
        this.dom.setAttribute('data-converted', converted);
        this.dom.setAttribute('data-writable', this.writable ? '1' : '0');
        this.dom.innerHTML = `<div class='item-status-bar'>
                    <div class='icon icon-converted'></div>
                    <p class='item-name'>${filename}</p>
                </div>
                <div class='list-col main-media'>
                    ${media}
                </div>
                <input id='${e_id}' class='converter-item-checkbox' type='checkbox' name='ids[]' value='${id}' ${converted == -1 ? 'disabled' : ''}>
                <label class='list-col checkbox-label icon' for='${e_id}'></label>`;
    }
    renderMedia(src, proportion){
        const media = this.type === 'image' ? this.renderImg(src) : this.renderVid(src);
        const padding = `${proportion * 100}%`;
        return `<div class='media-wrapper' data-proportion='${proportion}'><div class='media-spec-wrapper' style='--proportion: ${padding};'>${media}</div></div>`;
    }
    renderImg(src) {
        return `<img src='${src}' />`;
    }
    
    renderVid(src) {
        return `<video src='${src}' muted></video>`;
    }
    getElements(){
        this.input = this.dom.querySelector('input');
    }
    addListeners(){
        this.input.onchange = this.handleChange.bind(this);
    }
    handleChange(){
        this.checked = this.input.checked;
        if(typeof this.callback === 'function') 
            this.callback(this.input, this.converted);
    }
    getFilename(id, ext){
        id = String(id);
        while(id.length < 5) {
            id = '0' + id;
        }
        return id + '.' + ext;
    }
    updateConverted(to){
        this.converted = to;
        this.dom.setAttribute('data-converted', (this.converted ? '1' : '0'));
    }
    updateChecked(to){
        this.input.checked = false;
        this.handleChange();
    }
    addTo(parent){
        parent.appendChild(this.dom);
    }
    
}