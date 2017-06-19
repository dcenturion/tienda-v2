  function parsemessage(message) {
    var cadenas = {
                '(Y)': 'emotico-like',
                'O:)': 'emotico-angel',
                ':3': 'emotico-cari',
                'o.O': 'emotico-ojos1',
                'O.o': 'emotico-ojos2',
                ":'(": 'emotico-lloron',
                '3:)': 'emotico-diablo',
                ':(': 'emotico-triste',
                ':O': 'emotico-sorprendido',
                ':o': 'emotico-sorprendido',
                '8)': 'emotico-nerd',
                ':D': 'emotico-feliz1',
                '>_<': 'emotico-ext1',
                '<3': 'emotico-corazon',
                '^_^': 'emotico-feliz2',
                ':*': 'emotico-beso',
                ':v': 'emotico-boca',
                ':pinguino:': 'emotico-pinguino',
                ':poop:': 'emotico-poop',
                ':man:': 'emotico-man',
                ':tiburon:': 'emotico-tiburon',
                ':)': 'emotico-contento',
                '-_-': 'emotico-molesto',
                'B|': 'emotico-elegante',
                ':p': 'emotico-lengua',
                ':/': 'emotico-duda',
                '>.<': 'emotico-ext2',
                ';)': 'emotico-guino',
                ':robot:': 'emotico-robot',
                '(N)': 'emotic-manoabajo',
                ':aplauso:': 'emotic-aplauso',
                ':bomba:': 'emotic-bomba',
                ':fantasma:': 'emotic-fantasma',
                ':golpe:': 'emotic-golpe',
                ':pelota:': 'emotic-pelota',
                ':calabaza:': 'emotic-calabaza',
                ':arbol:': 'emotic-arbol',
                ':fuerza:': 'emotic-fuerza'
              };
 
    var newcadena = message;
    $.each(cadenas, function(key, value) {
      newcadena = newcadena.split(' ').map(function(cadena) {
        if(cadena === key ) {
            return '<i class="' + value + '" ></i>'
        } else {
            return cadena
        }
      }).join(' ')
    }); 
 
    return newcadena;
  }