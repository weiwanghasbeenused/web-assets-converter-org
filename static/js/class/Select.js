export default class Select{
    constructor(options, id = '', selected = '', msg={}, extraClass=[], extraWrapperClass = []){
        if(!options || !options.length) return;

        this.id = id;
        this.options = options;
        this.msg = {
            'pending': msg?.pending ? msg.pending : 'Click to select',
            'intro'  : msg?.intro ? msg.intro : '',
            'outro'  : msg?.outro ? msg.outro : '',
            'label'  : msg?.label ? msg.label : ''
        }
        this.elements = {};
        this.dom = null;
        this.expanded = false;
        this.selected = 0;
        this.borderWidth = 2;
        this.cls = ['pseudo-select', ...extraClass];
        this.w_cls = ['pseudo-select-wrapper', ...extraWrapperClass];
        if(typeof selected !== 'undefined') {
            for(let i = 0; i < this.options.length; i++) {
                if(this.options[i]['value'] == selected) this.selected = i;
            }
        }
        this.renderElements();
        this.getElements();
        this.addListeners();
        this.setValues();
    }
    renderElements(){
        this.dom = document.createElement('div');
        this.dom.id = this.id;
        this.dom.className = this.cls.join(' ');
        this.wrapper = document.createElement('div');
        this.wrapper.id = this.id + '-wrapper'; 
        this.wrapper.className = this.w_cls.join(' ');
        let w_html = '', html = '';
        if(this.msg.intro) w_html += '<div class="pseudo-select-intro">'+this.msg.intro+'</div>';
        if(this.msg.label ) w_html += `<div class="pseudo-select-label-wrapper"><label class="pseudo-select-label" for="${this.id}">${this.msg.label}</label></div>`;
        
        let options = '';
        let selected = '<div class="pseudo-option pseudo-select-toggle greyed-out">'+this.msg.pending+'</div>'
        for(let i = 0; i < this.options.length; i++) {
            if(i !== this.selected) {
                options += '<div data-value="'+this.options[i]['value']+'" class="pseudo-option">'+this.options[i]['display']+'</div>';
            }
            else {
                options += '<div data-value="'+this.options[i]['value']+'" class="pseudo-option selected">'+this.options[i]['display']+'</div>';
                selected = '<div class="pseudo-option pseudo-select-toggle">'+this.options[i]['display']+'</div>';
            }
        }
        html += selected + '<div class="pseudo-option-container">' +options + '</div>';
        this.dom.innerHTML = html;
        this.wrapper.innerHTML = w_html;
        this.wrapper.appendChild(this.dom);
    }
    getElements(){
        this.elements.toggle = this.dom.querySelector('.pseudo-select-toggle');
        this.elements.options = this.dom.querySelectorAll('.pseudo-option[data-value]');
    }
    addListeners(){
        this.elements.toggle.addEventListener('click', function(){
            if(!this.expanded) this.expand();
            else this.collapse();
        }.bind(this));
        for(let i = 0; i < this.elements.options.length; i++) {
            this.elements.options[i].addEventListener('click', function(){
                this.updateOption(i);
            }.bind(this));
        }
        window.addEventListener('resize', ()=>{
            this.dom.style.setProperty('--min-width', 0);
            this.setValues();
        })
        
    }
    updateOption(idx){
        this.selected = idx;
        this.dom.querySelector('.pseudo-option.selected').classList.remove('selected');
        this.elements.toggle.innerText = this.options[idx]['display'];
        this.elements.options[idx].classList.add('selected');
        this.collapse();
        // console.log(this.onChange);
        if(typeof this.onChange === 'function')
            this.onChange(this.options[idx].value);
    }
    applyDefault(){
        this.updateOption(this.selected);
    };
    addTo(parent) {
        parent.appendChild(this.wrapper);
    }
    expand(){
        this.expanded = true;
        this.dom.classList.add('expanded');
        this.removeClickOutside = this.onClickOutside(this.dom, ()=>{
            this.collapse();
        })
    }
    collapse(){
        this.expanded = false;
        this.dom.classList.remove('expanded');
        this.removeClickOutside();
        this.removeClickOutside = null;
    }
    applyCSS(){
        // ...
    }
    onClickOutside(targetElement, callback) {
        console.log('onClickOutside');
        function handleClick(event) {
            console.log('handleClick');
          if (!targetElement.contains(event.target)) {
            callback(event);
          }
        }
      
        document.addEventListener('click', handleClick);
      
        return () => {
          // Return a cleanup function to remove the listener when needed
          document.removeEventListener('click', handleClick);
        };
    }
    setValues(){
        this.dom.style.setProperty('--border-width', this.borderWidth + 'px');
        requestAnimationFrame(()=>{
            let min_width = 0;
            for(const option of this.elements.options) {
                min_width = Math.max(min_width, option.offsetWidth);
            }
            this.dom.style.setProperty('--min-width', parseInt(min_width + this.borderWidth * 2 + 1) + 'px');
        })
    }
}