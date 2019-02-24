
let appParserEmail = function($){

    (function($){
        $.fn.validatePattern = function(search) {
            // Get the current element's siblings
            var pattern = this.attr('pattern');
            var value = this.val();
    
            return !(pattern && (value.length > 0) && !value.match( new RegExp('^' + pattern + '$')));
        };
    })($);

    /**
     * @constructor AppParserEmail
     * 
     * @author Sergey Kozhedub <malati4ik123@gmail.com>
     */
    let _appParserEmail = function(){ 

        this.settings = {};
    
    } 
    
    _appParserEmail.prototype.runParser_submit = function(selectors, callback = false){

        $(selectors.formp_parser).off('submit').submit(e => {

            e.preventDefault();

            let data = {nesting: null, url: null};

            data.nesting    = $(e.target).find(selectors.input_nesting);
            data.url        = $(e.target).find(selectors.input_url);

            try{
                // Проверить все поля на валидацию и отправляем данные на сервер, 
                // В противном случае заканчиваем работу
                $.each(data, (i, el) => {
                    if(el.validatePattern()){
                        data[i] = el.val();
                    }else{
                        throw BreakException('All fields are not filled.');
                    }
                });
            }catch(e){
                return alert('All fields are not filled.');
            }

            $.ajax({
                url: $(e.target).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: data,
                success: callback === false ? (e) => {

                    

                } : callback
            });

            return false;

        });

    }

    _appParserEmail.prototype.events = function(){
    
        this.runParser_submit(this.settings.selectors);
    
    } 
    
    _appParserEmail.prototype.init = function(option){
    
        if(('selectors' in option) === false) return console.log('Not all settings!');
    
        let _is_option_selectors = ['formp_parser', 'input_url', 'input_nesting'];
    
        try{
            //Check the availability of selectors in the settings
            _is_option_selectors.forEach(el => {
                if((el in option.selectors) === false){
                    throw BreakException('Empty select ' . el);
                }
            });
    
        }catch(e){
            return console.log(e);
        }
    
        this.settings = option;
    
        this.events();
    
    }

    return _appParserEmail;

}($);
