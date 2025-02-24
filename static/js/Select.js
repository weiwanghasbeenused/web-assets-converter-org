class Select{
    constructor(wrapper, options, id = '', dft = '', msg){
        if(!wrapper || !options || !options.length) return;
        this.wrapper = wrapper;
        this.id = id;
        this.options = options;
        this.msg = {
            'pending': msg.pending ? msg.pending : 'Click to select',
            'intro'  : msg.intro,
            'outro'  : msg.outro
        }
        this.elements = {};
        this.selected = false;
        if(typeof dft !== 'undefined') {
            for(let i = 0; i < this.options.length; i++) {
                if(this.options[i]['value'] == dft) this.selected = i;
            }
        }
        console.log('this.selected = ' + this.selected);
        this.renderElements();
        this.getElements();
        this.addListeners();
        this.applyCSS();
    }
    renderElements(){
        let html = '';
        if(this.msg.intro) html += '<div class="pseudo-select-intro">'+this.msg.intro+'</div>';
        let options = '';
        let selected = '<div class="pseudo-option pseudo-select-toggle greyed-out">'+this.msg.pending+'</div>'
        html += '<div id="'+this.id+'" class="pseudo-select">'; 
        for(let i = 0; i < this.options.length; i++) {
            if(i !== this.selected) {
                options += '<div data-value="'+this.options[i]['value']+'" class="pseudo-option">'+this.options[i]['display']+'</div>';
            }
            else {
                options += '<div data-value="'+this.options[i]['value']+'" class="pseudo-option selected">'+this.options[i]['display']+'</div>';
                selected = '<div class="pseudo-option pseudo-select-toggle">'+this.options[i]['display']+'</div>';
            }
            
        }
        html += selected + '<div class="pseudo-option-container">' +options + '</div></div>';
        if(this.msg.outro) html += '<div class="pseudo-select-outro">'+this.msg.outro+'</div>';
        this.wrapper.innerHTML = html;
    }
    getElements(){
        this.elements.select = this.wrapper.querySelector('.pseudo-select');
        this.elements.toggle = this.wrapper.querySelector('.pseudo-select-toggle');
        this.elements.options = this.wrapper.querySelectorAll('.pseudo-option[data-value]');
    }
    addListeners(){
        this.elements.toggle.addEventListener('click', function(){
            this.elements.select.classList.toggle('expanded');
        }.bind(this));
        for(let i = 0; i < this.elements.options.length; i++) {
            this.elements.options[i].addEventListener('click', function(){
                this.updateOption(i);
            }.bind(this));
        }
        if(this.selected !== null) this.applyDefault();
    }
    updateOption(idx){
        this.selected = idx;
        this.wrapper.querySelector('.pseudo-option.selected').classList.remove('selected');
        this.elements.toggle.innerText = this.options[idx]['display'];
        this.elements.options[idx].classList.add('selected');
        this.elements.select.classList.remove('expanded');
        if(this.options[idx].callback) this.options[idx].callback();
    }
    applyDefault(){
        console.log('applyDefault: ' + this.selected)
        this.updateOption(this.selected);
    };
    applyCSS(){
        // ...
    }
}