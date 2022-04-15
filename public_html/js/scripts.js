/* global _ */

/**
 * scripts.js
 *
 * Wildlife Rehabilitation Center
 * by Milvus Group
 * 
 * Global JavaScript, if any.
 */

class Stopwatch {
    constructor(display, results) {
        this.running = false;
        this.display = display;
        this.results = results;
        this.laps = [];
        this.reset();
        this.print(this.times);
    }
    
    reset() {
        this.times = [ 0, 0, 0];
    }
    
    start() {
        if (!this.time) this.time = performance.now();
        if (!this.running) {
            this.running = true;
            requestAnimationFrame(this.step.bind(this));
        }
    }
    
    lap() {
        let times = this.times;
        if (this.running) {
            this.reset();
        }
        let li = document.createElement('li');
        li.innerText = this.format(times);
        this.results.appendChild(li);
    }
    
    stop() {
        this.running = false;
        this.time = null;
    }

    restart() {
        if (!this.time) this.time = performance.now();
        if (!this.running) {
            this.running = true;
            requestAnimationFrame(this.step.bind(this));
        }
        this.reset();
    }
    
    clear() {
        clearChildren(this.results);
    }
    
    step(timestamp) {
        if (!this.running) return;
        this.calculate(timestamp);
        this.time = timestamp;
        this.print();
        requestAnimationFrame(this.step.bind(this));
    }
    
    calculate(timestamp) {
        var diff = timestamp - this.time;
        // Hundredths of a second are 100 ms
        this.times[2] += diff / 10;
        // Seconds are 100 hundredths of a second
        if (this.times[2] >= 100) {
            this.times[1] += 1;
            this.times[2] -= 100;
        }
        // Minutes are 60 seconds
        if (this.times[1] >= 60) {
            this.times[0] += 1;
            this.times[1] -= 60;
        }
    }
    
    print() {
        this.display.innerText = this.format(this.times);
    }
    
    format(times) {
        return `\
${pad0(times[0], 2)}:\
${pad0(times[1], 2)}.\
${pad0(Math.floor(times[2]), 2)}`;
    }
}

function pad0(value, count) {
    var result = value.toString();
    for (; result.length < count; --count)
        result = '0' + result;
    return result;
}

function clearChildren(node) {
    while (node.lastChild)
        node.removeChild(node.lastChild);
}

$(document).ready(configure);


function configure()
{

    // configure typeahead
    // https://github.com/twitter/typeahead.js/blob/master/doc/jquery_typeahead.md

    /*$("a[data-target=#basicModal]").click(function(ev) {
        ev.preventDefault();
        var target = $(this).attr("href");
    // load the url and show modal on success
        $("#basicModal .modal-body").load(target, function() { 
            $("#basicModal").modal("show"); 
        });
    });*/


    $('#1-reg_date').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    if ($('#preregister').length) {
        preRegistration();
    }
    else {
        verifyRacenumber();
        teamsTypeahead();
        $('#category-select').change(function () {
            var selected = $("#category-select option:selected").text();
            extendForm('register-member-',selected);
        });
    }

    $('.profile-control').hide();

    $('.race-tables').hide();

    $('#general-link').click(function(ev) {
        raceMenu('#general');
    });
    $('#categories-link').click(function(ev) {
        raceMenu('#categories');
        categoriesTable();
    });
    $('#competitors-link').click(function(ev) {
        raceMenu('#competitors');
        competitorsTable();
    });
    $('#laps-link').click(function(ev) {
        raceMenu('#laps');
        resultsTable('laps');
    });
    $('#results-link').click(function(ev) {
        raceMenu('#results');
        resultsTable('final');
        $("#download-csv").click(function(){
                $("#final-table").tabulator("download", "csv", "data.csv");
        });
    });

    $('.start-button').click(function(ev) {
        ev.preventDefault();
        startLap(this.id);
    });

    $('#start-finish-form input').on('keypress', function(e) {
            return e.which !== 13;
    });

    $('#start-button').click(function(ev) {
        ev.preventDefault();
        $('#alert').hide();
        var rn = $('#race_number');

        var parameters = {
            race_number: rn.val(), 
            action: 'start',
            process: 'race'
        };
        $.getJSON("afuncs.php", parameters)
            .done(function(data, textStatus, jqXHR) {
                if (data == -1) {
                    $('#alert').show().html('Nincs ilyen rajtszám!');
                }
                else if (data == -2) {
                    $('#alert').show().html('A csapat már beérkezett!');
                }
                else if (data == -3) {
                    $('#alert').show().html('A csapat már elindult!');
                }
                else {
                    if (stopwatch !== false) {
                        stopwatch.restart();
                        $('#start-button').prop('disabled',true);
                        $('#finish-button').prop('disabled',false);
                    }
                    rn.prop('disabled',true);
                    console.log('teszt2');
                    $.each(data,function(i,d) {
                        initFinish();
                    });
                    //                    $('#finish-table').tabulator('addRow',d);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                // log error to browser's console
                console.log(errorThrown.toString());
            });
    });

    $('#finish-button').click(function(ev) {
        ev.preventDefault();
        $('#alert').hide();
        var rn = $('#race_number');
        rn.prop('disabled',false);
        var parameters = {
            race_number: rn.val(), 
            action: 'finish',
            process: 'race'
        };
        $.getJSON("afuncs.php", parameters)
            .done(function(data, textStatus, jqXHR) {
                if (data == -1) {
                    $('#alert').show().html('Nincs ilyen rajtszám!');
                }
                else if (data == -2) {
                    $('#alert').show().html('A csapat már beérkezett!');
                }
                else {
                    if (stopwatch) {
                        stopwatch.stop();
                        $('#finish-button').prop('disabled',true);
                        $('#start-button').prop('disabled',false);
                    }
                    rn.val('').focus();
                    $.each(data,function(i,d) {
                        initFinish();
                        //                    $('#finish-table').tabulator('addRow',d);
                    });
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                // log error to browser's console
                console.log(errorThrown.toString());
            });
        //        finish(rn.val(),stopwatch);
    });


    $('#finish-lap').click(function(ev) {
        ev.preventDefault();

        var message = 'Biztos le akarod zárni a futamot?';
        if (confirm(message) == true) {
            window.location = this.href;
            /*            $.get('afuncs.php',{process: 'race', action: 'finish-lap'})
                .done(function(data, textStatus, jqXHR) {
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown.toString());
                });*/
        }
    });
    var st = $('.stopwatch').length;
    if (st != 0) {
        var stopwatch = new Stopwatch(
            document.querySelector('.stopwatch'),
            document.querySelector('.results'));
    }
    else 
        var stopwatch = false;
    console.log(stopwatch);
    if ($('.finish').length > 0)
        initFinish();

    if ($('div.start-finish').length > 0) {
        initFinish();
        $('#finish-button').prop('disabled',true);
    }
}

function initFinish() {
    $("#finish-table").tabulator({
        fitColumns:true, 
        columns:[ 
            {title:"id",field:"id",visible:false},
            {title:"Kategória",field:"shortname",align:"center"},
            {title:"Rajtszám",field:"race_number",align:"center"},
            {title:"Futamidő",field:"finish_time",align:"center"},
            {title:"Büntetőpontok",field:"penality",editable:"true",align:"center",width:"150"},
            {title:"Végső idő",field:"final_time",align:"center"}
        ],
        cellEdited:function(id,data,row){
            var id = $('[data-id="'+id+'"]>[data-field="id"]').data('value');
            var rn = $('[data-id="'+id+'"]>[data-field="race_number"]').data('value');
            var message = row + "büntetőpont a(z) "+rn +"csapatnak.";
            var parameters = {
                id: id,
                value: row,
                action: 'penality',
                process: 'race'
            };
            $.post("afuncs.php", parameters)
                .done(function(data, textStatus, jqXHR) {
                    initFinish();
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    // log error to browser's console
                    console.log(errorThrown.toString());
                });
        }
    });
    var parameters = {
        action: 'initfinish',
        process: 'race'
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            $('#finish-table').tabulator('setData',data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}

function start(rn,stopwatch = false) {
    var parameters = {
        race_number: rn, 
        action: 'start',
        process: 'race'
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            if (data == -1) {
                $('#alert').show().html('Nincs ilyen rajtszám!');
            }
            else if (data == -2) {
                $('#alert').show().html('A csapat már beérkezett!');
            }
            else if (data == -3) {
                $('#alert').show().html('A csapat már elindult!');
            }
            else {
                if (stopwach)
                    stopwatch.restart();
                $.each(data,function(i,d) {
                    initFinish();
                    //                    $('#finish-table').tabulator('addRow',d);
                });
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}
function finish(rn,stopwatch) {
    var parameters = {
        race_number: rn, 
        action: 'finish',
        process: 'race'
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            if (data == -1) {
                $('#alert').show().html('Nincs ilyen rajtszám!');
            }
            else if (data == -2) {
                $('#alert').show().html('A csapat már beérkezett!');
            }
            else {
                if (stopwatch)
                    stopwatch.stop();
                $.each(data,function(i,d) {
                    initFinish();
                    //                    $('#finish-table').tabulator('addRow',d);
                });
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}

function startLap (id) {
    $('#'+id).attr('disabled','disabled');
    var parameters = {
        cat_id: id, 
        action: 'start',
        process: 'race'
    };
    $.get("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            $('#starttime-'+id).html(data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}


function raceMenu(id) {
    $('.race-tables').hide();
    $(id).show();
}

function competitorsTable() {
    $("#competitors").tabulator({
        fitColumns:true, 
        groupBy:"shortname",
        columns:[ 
            {title:"tid",field:"tid", visible:false},
            {title:"mid",field:"mid", visible:false},
            {title:"Rajtszám",field:"race_number"},
            {title:"Csapatnév",field:"team_name"},
            {title:"Vezetéknév",field:"last_name",editable:"true"},
            {title:"Keresztnév",field:"first_name",editable:"true"},
            {title:"Regisztrálás időpontja",field:"reg_date",editable:"true"},
            {title:"Illeték",field:"fee",formatter:"money",editable:"true"},
            {title:"Fizetve", field:"fee_paid",formatter:"tickCross",editable:"true", align:"center",width:"70",sortable:"false"}
        ],

        cellEdited:function(id,data,row){
            var mid = $('[data-id="'+id+'"]>[data-field="mid"]').data('value');
            console.log(id);
            var message = "Biztos szerkeszted a ??? sort? ";
            if (confirm(message) == true) {
                var parameters = {
                    mid: mid,
                    column: data,
                    value: row,
                    table: 'team_members',
                    process: 'edit'
                };
                $.post("afuncs.php", parameters)
                    .done(function(data, textStatus, jqXHR) {
                        console.log(data);
                        if (data == 1)
                            console.log('Versenyző szerkesztve');
                        else if (data == 2) {
                            categoriesTable();
                            console.log('Új kategória hozzáadva');
                        }
                        else                            
                            alert('ERROR!');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        // log error to browser's console
                        console.log(errorThrown.toString());
                    });
            }               
        }

    });
    var parameters = {
        process: 'race-control',
        action: 'competitors-table'
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            $('#competitors').tabulator("setData", data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });

}

function categoriesTable() {
    $("#categories-table").tabulator({
        fitColumns:true, //fit columns to width of table (optional)
        columns:[ //Define Table Columns
            {title:"cat_id",field:"cat_id", visible:false},
            {title:"Név",field:"name", editable: true},
            {title:"Rövidítés",field:"shortname", editable: true},
            {title:"Csónak típusa",field:"boat_type", editable: true},
            {title:"Helyek száma",field:"seats", editable: true},
            {title:"Csónak hossza",field:"boat_length", editable: true},
            {title:"Törlés",width:"70",align:"center",formatter:"buttonCross", onClick:function(e, cell, value, data) {
                deleteDbRow('categories',data['cat_id']);
                $("#categories-table").tabulator("deleteRow", data['_index']);
            }}
        ],


        cellEdited:function(id,data,row){
            var cat_id = $('[data-id="'+id+'"]>[data-field="cat_id"]').data('value');
            var message = "Biztos szerkeszted a ??? sort? ";
            if (confirm(message) == true) {
                var parameters = {
                    cat_id: cat_id,
                    column: data,
                    value: row,
                    table: 'categories',
                    process: 'edit'
                };
                $.post("afuncs.php", parameters)
                    .done(function(data, textStatus, jqXHR) {
                        if (data == 1)
                            console.log('Kategória szerkesztve');
                        else if (data == 2) {
                            categoriesTable();
                            console.log('Új kategória hozzáadva');
                        }
                        else                            
                            alert('ERROR!');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        // log error to browser's console
                        console.log(errorThrown.toString());
                    });
            }               
        }
    });

    var parameters = {
        process: 'race-control',
        action: 'categories-table'
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            $('#categories-table').tabulator("setData", data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });

    $("#add-row").click(function(){
        $("#categories-table").tabulator("addRow");
    });
}

/*------------------------
 * race results builder
 * table = 'final' or table = 'laps'
 * -----------------------*/

function resultsTable(table) {
    var gr;
    var srt;
    if (table == 'final'){
        gr = 'shortname';
        srt = 'final_time';
    }
    else {
        gr = 'lap_no';
        srt = 'lap_no';
    }
    $("#"+table+"-table").tabulator({
        fitColumns:true, 
        groupBy: gr,
        sortBy: srt,
        sortDir: 'asc',
        columns:[ 
            {title:"cat_id",field:"cat_id",visible:false},
            {title:"tid",field:"tid",visible:false},
            {title:"id",field:"id",visible:false},
            {title:"Kategória",field:"shortname",align:"center"},
            {title:"Rajtszám",field:"race_number",align:"center"},
            {title:"Futam",field:"lap_no",align:"center"},
            {title:"Csapattagok",field:"team_members",align:"center"},
            {title:"Futamidő",field:"finish_time",align:"center"},
            {title:"Büntetőpontok",field:"penality",editable:"true",align:"center",width:"150"},
            {title:"Végső idő",field:"final_time",align:"center"}
        ],
        cellEdited:function(id,data,row){
            var id = $('[data-id="'+id+'"]>[data-field="id"]').data('value');
            var rn = $('[data-id="'+id+'"]>[data-field="race_number"]').data('value');
            var message = row + "büntetőpont a(z) "+rn +"csapatnak.";
            var parameters = {
                id: id,
                value: row,
                action: 'penality',
                process: 'race'
            };
            $.post("afuncs.php", parameters)
                .done(function(data, textStatus, jqXHR) {
                    resultsTable(table);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    // log error to browser's console
                    console.log(errorThrown.toString());
                });
        }
    });
    var parameters = {
        action: table+'-results',
        process: 'race'
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            $('#'+table+'-table').tabulator('setData',data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}


function deleteDbRow(table,id) {
    var message = "Biztos törlöd?";
    if (confirm(message) == true) {
        var parameters = {
            table: table,
            id: id,
            process: 'edit'
        };
        $.post("afuncs.php", parameters)
            .done(function(data, textStatus, jqXHR) {
                console.log(data);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                // log error to browser's console
                console.log(errorThrown.toString());
            });
    }
}









/*************************************************************
 * Form függvények
 *
 ************************************************************/

/*function addContact () {
    var form = $('#add-new-contact');
    var formData = $(form).serialize();
    $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData
    })
    .done(function(response) {
        $('.modal-body').html('<h4>Kapcsolat hozzáadva</h4><button type="button" class="close" data-dismiss="modal" aria-hidden="true">OK</button></br>');
    })
    .fail(function(data) {
// log error to browser's console
        console.log('error');
    });
}
*/


/* Regisztációs form kibővítése */

function extendForm (form,category,callback) {
    var parameters = {
        category: category,
        process: 'extendform',
        form: form 
    };
    $.get("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            $('#team-members').html(data);
            callback();
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}



/*********************************************************
 * Fotó kezelés
 *
 ***********************************************************/

/*function deletePhoto(file_path) {
    if (confirm("Biztos törlöd a képet?") == true) {
        var parameters = {
            photo: file_path,
            process: 'delete-photo'
        };
        $.get("afuncs.php", parameters)
            .done(function(data, textStatus, jqXHR) {
                $('#'+file_path.replace('/','_').replace('.','_')).hide();
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
// log error to browser's console
                console.log(errorThrown.toString());
            });
    }
}
*/












/*********************************************************
 * Adatbazis függvények
 *
 ************************************************************/
















/**********************************************************
 * Typeahead függvények
 *
 *********************************************************/
function preRegistration() {
    $("#race_number").typeahead(
        {
            autoselect: true,
            highlight: true,
            minLength: 1
        },
        {
            source: searchRacenumberPrereg
        }
    );

    $("#team_name").typeahead(
        {
            autoselect: true,
            highlight: true,
            minLength: 1
        },
        {
            source: searchTeamsPrereg
        }
    );
    $('#race-select').change(function () {
        var selected = $("#race-select option:selected").val();
        getCategories(selected);
    });
    $('#category-select').change(function () {
        var selected = $("#category-select option:selected").text();
        extendForm('preregister-member-',selected);
    });
}

function getCategories(race) {
    var parameters = {
        process: 'getCategories',
        race: race
    }
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            $('#category-select').html('');
            $.each(data, function(key, value) {   
                $('#category-select')
                    .append($("<option></option>")
                        .attr("value",value.shortname)
                        .text(value.shortname)); 
            });
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}

function searchRacenumberPrereg(selected) {
    var parameters = {
        race_number: selected,
        process: 'verifyracenumberPrereg'
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            if (data == '1') {
                $('.registration #race_number').css('border-color','');
                $('#registration-button').removeAttr('disabled');
            }
            else {
                $('.registration #race_number').css('border-color','red');
                $('#registration-button').attr('disabled','disabled');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}
function searchTeamsPrereg(query)
{
    // get places matching query (asynchronously)
    var parameters = {
        team_name: query,
        process: 'preregistration',
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            // call typeahead's callback with search results (i.e., places)
            if (data == '1') {
                $('.registration #team_name').css('border-color','');
                $('#registration-button').removeAttr('disabled');
            }
            else {
                $('.registration #team_name').css('border-color','red');
                $('#registration-button').attr('disabled','disabled');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}


function verifyRacenumber () {
    $("#race_number").typeahead({
        autoselect: true,
        highlight: true,
        minLength: 1
    },
        {
            source: searchRacenumber
        });
}

function searchRacenumber(selected) {
    var parameters = {
        race_number: selected,
        process: 'verifyracenumber'
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            if (data == '1') {
                $('.registration #race_number').css('border-color','');
                $('.finish #race_number').css('border-color','red');
                $('#start-finish-form #race_number').css('border-color','red');
                $('#registration-button').removeAttr('disabled');
            }
            else {
                $('.registration #race_number').css('border-color','red');
                $('.finish #race_number').css('border-color','');
                $('#start-finish-form #race_number').css('border-color','');
                $('#registration-button').attr('disabled','disabled');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}

function teamsTypeahead () {
    $("#team_name").typeahead({
        autoselect: true,
        highlight: true,
        minLength: 2
    },
        {
            source: searchTeams,
            templates: {
                empty: "nincs ilyen csapat",
                suggestion: _.template("<p><%- team_name %></p>")
            }
        });
    var properties = ['team_name','first_name','last_name','country','region','city','address','postal_code','phone','email','reg_date'];
    $("#team_name").on("typeahead:selected", function(eventObject, suggestion) {
        $.getJSON("afuncs.php", 
            {
                teamname: suggestion.team_name,
                process: 'registration',
                action: 'getDetails'
            })
            .done(function(data, textStatus, jqXHR) {
                $("#team_name").typeahead("val", suggestion.team_name);
                $("[name=category] option").filter(function() { 
                    return ($(this).text() == suggestion.category);
                }).prop('selected', true);
                $('#race_number').val(suggestion.race_number);
                extendForm('register-member-',suggestion.category, function() {
                    var i = 0;
                    data.forEach(function(el) {
                        i++;
                        $.each(el,function(key,v) {
                            console.log(v);
                            $('#'+i+'-'+key).val(v);
                        });
                    });
                });
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                // log error to browser's console
                console.log(errorThrown.toString());
            });
    });
}
function searchTeams(query, cb)
{
    // get places matching query (asynchronously)
    var parameters = {
        teamname: query,
        process: 'registration',
        action: 'typeahead'
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
            // call typeahead's callback with search results (i.e., places)
            cb(data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // log error to browser's console
            console.log(errorThrown.toString());
        });
}
/*function contactsTypeahead () {
    $("#found_by_id").typeahead({
            autoselect: true,
            highlight: true,
            minLength: 2 
        },
        {
            source: searchContacts,
            templates: {
                empty: "nobody matches",
                suggestion: _.template("<p><%- name %></p>")
        }
    });

    $("#found_by_id").on("typeahead:selected", function(eventObject, suggestion) {
        $("#found_by_id").typeahead("val", suggestion.name);
    });
}

function searchContacts(query, cb)
{
    var county = $('#county').val();
// get places matching query (asynchronously)
    var parameters = {
        name: query,
        process: 'contacts' 
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
// call typeahead's callback with search results (i.e., places)
            cb(data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
// log error to browser's console
            console.log(errorThrown.toString());
        });
}

function placesTypeahead () {
    $("#location").typeahead({
            autoselect: true,
            highlight: true,
            minLength: 3 
        },
        {
            source: searchPlaces,
            templates: {
                empty: "no locations found yet",
                suggestion: _.template("<p><%- name %>, <%- county %>, <%- postalCode %></p>")
        }
    });

    $("#location").on("typeahead:selected", function(eventObject, suggestion) {
        $("#location").typeahead("val", suggestion.name);
        $("#x").val(suggestion.X);
        $("#y").val(suggestion.Y);
        $("#postal_code").val(suggestion.postalCode);

    });
}

function searchPlaces(query, cb)
{
    var county = $('#county').val();
// get places matching query (asynchronously)
    var parameters = {
        place: query,
        county: county,
        process: 'places' 
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
// call typeahead's callback with search results (i.e., places)
            cb(data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
// log error to browser's console
            console.log(errorThrown.toString());
        });
}

function searchSpecies(query, cb)
{
// get places matching query (asynchronously)
    var parameters = {
        species: query,
        process: 'species' 
    };
    $.getJSON("afuncs.php", parameters)
        .done(function(data, textStatus, jqXHR) {
// call typeahead's callback with search results (i.e., places)
            cb(data);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
// log error to browser's console
            console.log(errorThrown.toString());
        });
}
*/


