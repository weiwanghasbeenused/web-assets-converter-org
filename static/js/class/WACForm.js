import WACItem from './WACItem.js';
import Select from './Select.js';

export default class WACForm{
    constructor(parent, id, meta){
        if(!parent) return;
        this.parent = parent;
        this.auth = btoa(meta['username'] + ':' + meta['password']);
        this.imgFormats = meta['imgFormats'];
        this.vidFormats = meta['vidFormats'];
        this.filters = this.setupFilters();
        this.id = id ? id : 'wac-form-1';
        this.num_per_fetch = 5;
        this.num_fetched = 0;
        this.isFetching = false;
        this.fetchedAll = false;
        this.fetched = new Map();
        this.status = '';
        this.items = [];
        this.checked = {
            'converted': [],
            'not-converted': [] 
        }
        this.endpoints = {
            'unconvert': window.location.origin + '/web-assets-converter/api/unconvert',
            'list': window.location.origin + '/web-assets-converter/api/list',
            'convert': window.location.origin + '/web-assets-converter/api/convert'
        }
        this.messages = {
            'no-item': 'There\'s no item that fits the filters.',
            'pending-request': 'Processing your request . . .'
        } 
        this.announcements = {
            'pending-request': {
                content: 'Processing your request . . .',
                button: []
            }
        }
        this.scroll_timer = null;
        this.renderElements();
        this.getElements();
        this.fetchItems();
        this.addListeners();
    }
    setupFilters(){
        const filters = [
            {
                'id': 'converted',
                'display': 'Status',
                'options': [
                    {
                        'key': 'option-converted-all',
                        'display': 'All',
                        'value': ''
                    },
                    {
                        'key': 'option-converted-converted',
                        'display': 'Converted',
                        'value': '1'
                    },{
                        'key': 'option-converted-unconverted',
                        'display': 'Unconverted',
                        'value': '0'
                    }
                ],
                'current': ''
            },
            {
                'id': 'type',
                'display': 'Type',
                'options': [
                    {
                        'key': 'option-type-all',
                        'display': 'All',
                        'value': ''
                    },
                    {
                        'key': 'option-type-images',
                        'display': 'Images',
                        'value': 'images'
                    },{
                        'key': 'option-type-videos',
                        'display': 'Videos',
                        'value': 'videos'
                    }
                ],
                'current': ''
            }
        ];

        const params = new URLSearchParams(window.location.search);
        for(const filter of filters) {
            const string = params.get(filter['id']);
            if(!string) {
                filter.current = filter.options[0].value;
                continue;
            }
            for(const option of filter['options']) {
                if(string === option.value) {
                    filter.current = option.value;
                    break;
                }
            }
        } 
        return filters;
    }
    fetchItems(url){
        if(this.fetchedAll) return;
        if(!url) 
            url = this.generateListRequestUrl('list');
        this.isFetching = true;
        this.request(url, 'GET', null, this.handleListResult.bind(this))
    }
    generateListRequestUrl(){
        const queries = [`limit=${this.num_per_fetch}`, `offset=${this.num_fetched}`];
        for(const filter of this.filters) {
            if(filter.current === '') continue;
            queries.push(`${filter.id}=${filter.current}`);
        }
        return this.endpoints['list'] + '?' + queries.join('&');
    }
    handleListResult(result){
        this.isFetching = false;
        const fetched = result['data'];
        console.log('>>> handleListResult');
        if(fetched.length === 0 ) {
            console.log('    fetched.length === 0');
            if(this.num_fetched === 0)
                this.itemsContainer.innerHTML = `<div class="wac-message-container">${this.messages['no-item']}</div>`;  
            this.fetchedAll = true;
        } else {
            const resetList = this.num_fetched === 0;
            this.updateList(fetched, resetList);
            console.log('    ' + fetched.length + ' vs ' + this.num_per_fetch);
            if(fetched.length < this.num_per_fetch) this.fetchedAll = true;
        }
        this.form.setAttribute('data-fetched-all', this.fetchedAll ? '1' : '0');
        this.num_fetched += fetched.length;

        if(!this.fetchedAll && this.itemsContainer.offsetHeight < window.innerHeight) {
            this.fetchItems();
        }
    }
    renderElements(){
        this.controls = this.renderControls();
        this.list = this.renderList();
        this.form = document.createElement('form');
        this.form.id = this.id;
        this.form.className = 'wac-form';
        this.form.appendChild(this.controls);
        this.form.appendChild(this.list);
        this.announcement = this.renderAnnouncement();
        this.container = document.createElement('div');
        this.container.className = 'wac-container';
        this.container.appendChild(this.form);
        this.container.appendChild(this.announcement);
        this.parent.appendChild(this.container);
    }
    renderControls(){
        const output = document.createElement('div');
        output.className = 'controls';
        const filters = this.renderFilters();
        const actions = this.renderActions();
        output.appendChild(filters);
        output.appendChild(actions);
        return output;
    }
    renderList(){
        const output = document.createElement('div');
        output.className = 'list';
        output.innerHTML = `<div class="items-container"></div>
        <div class="list-bottom-message">
            <div class="list-bottom-message-icon"><div class="circle"></div><div class="circle"></div><div class="circle"></div></div>
        </div>`;
        return output;
    }
    renderFilters(){
        const output = document.createElement('div');
        output.className = 'filters';
        for(const data of this.filters) {
            this.renderFilter(data, output);
            // output.appendChild(rendered);
        }
        return output;
    }
    renderFilter(data, parent){
        const select_id = data.id;
        const select = new Select(data.options, select_id, data.current, { 'label': data.display }, ['filter'], ['filter-wrapper']);
        select.onChange = (value)=>{ 
            this.applyFilter(data.id, value); 
            this.updateUrl(data.id, value);
        };
        select.addTo(parent);
    }
    renderActions(){
        const output = document.createElement('div');
        output.className = 'actions';
        // output.setAttribute('data-action-status', this.actionStatus);
        const actions = [
            {
                'id': 'convert',
                'display': 'Convert',
                'value': 'convert'
            },
            {
                'id': 'unconvert',
                'display': 'Unconvert',
                'value': 'unconvert'
            }
        ]
        for(const data of actions) {
            const rendered = this.renderAction(data);
            output.appendChild(rendered);
        }
        return output;
    }
    renderAction(data){
        const output = document.createElement('div');
        output.className = 'action btn';
        output.id = data.id + '-btn';
        output.setAttribute('data-enabled', '0');
        output.innerText = data.display;
        output.onclick = ()=>{
            const enabled = !output.getAttribute('data-enabled') || output.getAttribute('data-enabled') === '1';
            if(!enabled) return;
            this[data['value']]();
        }
        return output;
    }
    renderAnnouncement(){
        const output = document.createElement('div');
        output.id = 'wac-announcement';
        const message = document.createElement('div');
        message.id = 'wac-announcement-message';
        const btns = document.createElement('div');
        btns.className = 'announcement-btn-container';
        const btn = document.createElement('div');
        btn.className = 'btn close-announcement-btn';
        btn.innerText = 'Okay';
        btns.appendChild(btn);
        output.appendChild(message);
        output.appendChild(btns)
        return output;
    }
    getElements(){
        this.announcement_message = this.container.querySelector('#wac-announcement-message');
        this.itemsContainer = this.list.querySelector('.items-container');
        this.loadingTrigger = this.list.querySelector('.list-bottom-message');
        this['convert-btn'] = this.container.querySelector('#convert-btn');
        this['unconvert-btn'] = this.container.querySelector('#unconvert-btn');
    }
    addListeners(){
        window.addEventListener('scroll', ()=>{
            this.bufferScroll();
        })
    }
    applyFilter(key, value){
        const find  = this.filters.find((item)=>item.id === key);
        if(!find) return;
        find.current = value;
        this.num_fetched = 0;
        this.items = [];
        this.fetchedAll = false;
        this.fetchItems();
    }
    updateUrl(key, value){
        const params = new URLSearchParams(window.location.search);
        if(value === '') params.delete(key);
        else params.set(key, value);
        window.history.pushState({}, '', window.location.pathname + '?' + params.toString());
    }
    updateList(items, resetList=false){
        const fragment = document.createDocumentFragment();
        for(const item of items) {
            const rendered = this.getRenderedItem(item);
            this.items.push(rendered);
            rendered.addTo(fragment);
        }
        if(resetList)
            this.itemsContainer.innerHTML = '';
        this.itemsContainer.appendChild(fragment); // Append all at once
    }
    getRenderedItem(item) {
        if (this.fetched.has(item.id)) {
            return this.fetched.get(item.id); // Return cached DOM element
        }
        const element = new WACItem(item, this.imgFormats, this.vidFormats, this.updateCheckedList.bind(this));
        this.fetched.set(item.id, element); // Cache it
        return element;
    }
    convert(){
        const data = new FormData(this.form);
        data.append('action', 'convert');
        this.updateStatus('pending-request');
        this.request(this.endpoints['convert'], 'POST', data, this.handleConvertResult.bind(this))
    }
    handleConvertResult(result){
        if(result['status'] === 'nothing-to-convert') {
            return;
        }
        const success = result['data']['success'];
        const fail = result['data']['fail'];
        for(const item of success) {
            const id = item['id'];
            const list_item = this.items.find(el=>el.media_id == id );
            if(!list_item) continue;
            list_item.updateChecked(false);
            list_item.updateConverted(true);
        }
        this.updateStatus('');
    }
    unconvert(){
        const data = new FormData(this.form);
        data.append('action', 'unconvert');
        this.updateStatus('pending-request');
        this.request(this.endpoints['unconvert'], 'POST', data, this.handleUnconvertResult.bind(this))
    }
    handleUnconvertResult(result){
        const success = result['data']['success'];
        const fail = result['data']['fail'];
        for(const item of success) {
            const id = item['id'];
            const list_item = this.items.find(el=>el.media_id == id );
            if(!list_item) continue;
            list_item.updateChecked(false);
            list_item.updateConverted(false);
        }
        this.updateStatus('');
    }
    inspect(item, action){
        
    }
    request(endpoint, method, data, cb){
        
        const options = {method: method};
        if(method === 'POST' && data) options['body'] = data;
        fetch(endpoint, options)
        .then((response)=>{
            if(!response.ok) {
                console.log('response not okay');
            }
            return response.json();
         })
         .then((result)=>{
            // console.log(result);
            // this.updateStatus('');
            if(typeof cb === 'function')
                cb(result);
         })
    }
    updateStatus(status){
        this.status = status;
        this.container.setAttribute('data-status', this.status);
        if(this.announcements[this.status]) {
            this.container.classList.add('viewing-announcement');
            this.announcement_message.innerHTML = this.announcements[this.status]['content'];
        } else {
            this.container.classList.remove('viewing-announcement');
        }
    }
    updateCheckedList(input, item_converted){
        // const input = e.target;
        const id = input.value;
        let arr = item_converted ? this.checked['converted'] : this.checked['not-converted'];
        let action_btn = this[`${item_converted ? 'unconvert' : 'convert' }-btn`];
        if(input.checked) {
            arr.push(id);
        } else {
            const idx = arr.indexOf(id);
            if(idx === -1) return;
            arr.splice(idx, 1);
        }
        if(arr.length === 0) {
            action_btn.setAttribute('data-enabled', 0);
        } else {
            action_btn.setAttribute('data-enabled', 1);
        }
    }
    bufferScroll(){
        if(this.scroll_timer)
            clearTimeout(this.scroll_timer);
        this.scroll_timer = setTimeout(()=>{
            this.fetchMoreItems();
        }, 200);
    }
    fetchMoreItems(){
        if(!this.scrollReachBottom(100) || this.isFetching) return;
        this.fetchItems();
    }
    scrollReachBottom(dev=0){
        const documentHeight = Math.max( document.body.scrollHeight, document.body.offsetHeight, 
            document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight );
        return window.innerHeight + window.scrollY + dev >= documentHeight;
    }
}