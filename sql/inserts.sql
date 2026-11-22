USE tecnostock;

-- Crear las categorías si todavía no existen
INSERT IGNORE INTO categorias (nombre, descripcion) VALUES
('Periféricos', 'Mouse, teclados y accesorios de entrada'),
('Audio', 'Audífonos, micrófonos y parlantes'),
('Cables', 'Cables y adaptadores'),
('Almacenamiento', 'Memorias, discos y unidades de almacenamiento');

-- Insertar 100 productos
INSERT IGNORE INTO productos
(codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria)
VALUES

-- ==================== PERIFÉRICOS ====================
('PER-101','Mouse inalámbrico','Mouse óptico inalámbrico con receptor USB',8990,32,8,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-102','Mouse gamer RGB','Mouse gamer con iluminación RGB y seis botones',15990,18,5,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-103','Mouse ergonómico','Mouse vertical para uso prolongado',18990,12,4,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-104','Mouse Bluetooth','Mouse Bluetooth compacto y portátil',11990,21,6,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-105','Teclado inalámbrico','Teclado inalámbrico con receptor USB',17990,16,5,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-106','Teclado mecánico RGB','Teclado mecánico con iluminación RGB',39990,9,3,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-107','Teclado compacto','Teclado compacto para escritorio',24990,14,4,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-108','Teclado numérico USB','Teclado numérico externo USB',8990,20,6,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-109','Teclado ergonómico','Teclado ergonómico para oficina',32990,7,2,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-110','Combo teclado y mouse','Set inalámbrico de teclado y mouse',22990,11,4,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-111','Mousepad básico','Alfombrilla para mouse de tela',3990,45,12,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-112','Mousepad gamer XL','Alfombrilla extendida para teclado y mouse',10990,22,6,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-113','Hub USB 4 puertos','Concentrador USB 3.0 de cuatro puertos',12990,17,5,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-114','Lector de tarjetas USB','Lector externo SD y microSD',6990,25,7,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-115','Webcam HD 720p','Cámara web HD con micrófono',16990,13,4,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-116','Webcam Full HD','Cámara web Full HD para videollamadas',27990,8,3,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-117','Soporte para notebook','Base ajustable para computador portátil',14990,19,5,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-118','Atril para monitor','Soporte elevador para monitor',19990,10,3,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-119','Lápiz óptico','Lápiz táctil capacitivo universal',5990,28,8,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-120','Control para presentaciones','Presentador inalámbrico con puntero',18990,6,2,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-121','Lector de códigos de barras','Lector alámbrico de códigos de barras USB',24990,6,2,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-122','Escáner compacto','Escáner USB compacto para documentos',89990,2,1,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-123','Reposamuñecas','Apoyo acolchado para teclado',5990,14,4,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-124','Switch USB','Conmutador USB para compartir periféricos',11990,7,2,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-125','Luz LED para monitor','Lámpara LED alimentada por USB',9990,12,4,(SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),

-- ==================== AUDIO ====================
('AUD-101','Audífonos Bluetooth','Audífonos inalámbricos con estuche',24990,14,5,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-102','Audífonos USB-C','Audífonos con conexión USB-C',9990,24,7,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-103','Audífonos gamer','Audífonos circumaurales para juegos',29990,10,3,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-104','Audífonos deportivos','Audífonos intrauditivos para deporte',13990,17,5,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-105','Audífonos diadema','Audífonos estéreo con cable',11990,20,6,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-106','Parlante Bluetooth','Parlante compacto recargable',22990,12,4,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-107','Parlantes USB para PC','Par de parlantes estéreo USB',14990,9,3,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-108','Parlante de escritorio','Sistema de audio estéreo para escritorio',19990,7,2,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-109','Micrófono USB','Micrófono USB para reuniones y grabación',34990,5,2,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-110','Micrófono corbatero','Micrófono de solapa con cable',8990,16,4,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-111','Adaptador Bluetooth USB','Receptor Bluetooth para computador',7990,18,5,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-112','Tarjeta de sonido USB','Adaptador externo de audio USB',6990,21,6,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-113','Cable auxiliar 3.5 mm','Cable de audio estéreo de un metro',2990,40,10,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-114','Audífonos aislantes','Audífonos cerrados para aislamiento',21990,8,3,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-115','Base para audífonos','Soporte de escritorio para audífonos',7990,15,4,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-116','Barra de sonido USB','Barra de sonido compacta para computador',24990,6,2,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-117','Auricular mono','Auricular mono con micrófono y cable',6990,12,4,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-118','Micrófono USB con brazo','Micrófono USB con brazo ajustable',49990,3,1,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-119','Parlante portátil','Parlante recargable para uso portátil',18990,11,3,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-120','Audífonos inalámbricos','Audífonos estéreo inalámbricos',27990,13,4,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-121','Micrófono condensador','Micrófono de condensador para escritorio',39990,5,2,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-122','Parlante estéreo','Parlante estéreo para computador',16990,10,3,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-123','Adaptador de audio','Adaptador USB para conexión de audio',4990,23,6,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-124','Audífonos con micrófono','Audífonos con micrófono integrado',14990,18,5,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-125','Micrófono de solapa','Micrófono compacto para grabaciones',10990,14,4,(SELECT id_categoria FROM categorias WHERE nombre='Audio')),

-- ==================== CABLES ====================
('CAB-101','Cable HDMI 2 m','Cable HDMI de alta velocidad de dos metros',5990,34,10,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-102','Cable HDMI 5 m','Cable HDMI de alta velocidad de cinco metros',9990,18,5,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-103','Cable DisplayPort','Cable DisplayPort para monitor',8990,16,5,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-104','Cable USB-A a USB-C','Cable de carga y datos USB-C',3990,50,12,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-105','Cable USB-C a USB-C','Cable USB-C de carga y datos',5990,31,8,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-106','Cable microUSB','Cable de carga microUSB',2990,27,8,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-107','Cable Lightning','Cable USB a conector Lightning',6990,19,5,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-108','Cable Ethernet 2 m','Cable UTP Cat 6 de dos metros',3990,38,10,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-109','Cable Ethernet 5 m','Cable UTP Cat 6 de cinco metros',5990,25,7,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-110','Cable Ethernet 10 m','Cable UTP Cat 6 de diez metros',8990,14,4,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-111','Adaptador USB-C a HDMI','Adaptador de video USB-C a HDMI',14990,11,3,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-112','Adaptador HDMI a VGA','Convertidor HDMI a VGA',10990,13,4,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-113','Adaptador USB-C a USB-A','Adaptador compacto USB-C a USB-A',4990,29,8,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-114','Adaptador USB a Ethernet','Adaptador de red USB 3.0 a RJ45',12990,9,3,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-115','Cable de poder PC','Cable de alimentación para computador',4990,22,6,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-116','Cable SATA III','Cable de datos SATA III',2990,33,9,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-117','Extensión USB','Extensión USB-A de dos metros',4990,20,6,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-118','Cable USB-C a DisplayPort','Cable USB-C a DisplayPort para monitor',17990,6,2,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-119','Adaptador USB-C multipuerto','Adaptador USB-C con HDMI y USB',29990,7,2,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-120','Cable USB-C 2 m','Cable USB-C reforzado de dos metros',6990,19,5,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-121','Cable HDMI 4K','Cable HDMI compatible con resolución 4K',7990,17,5,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-122','Adaptador USB-C a Ethernet','Adaptador de red USB-C a RJ45',16990,8,3,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-123','Cable óptico de audio','Cable óptico digital para equipos de sonido',5990,15,4,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-124','Cable de impresora USB','Cable USB para conexión de impresoras',3990,24,7,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),
('CAB-125','Cable de red Cat 6','Cable de red UTP categoría 6',4990,30,8,(SELECT id_categoria FROM categorias WHERE nombre='Cables')),

-- ==================== ALMACENAMIENTO ====================
('ALM-101','Memoria USB 16GB','Pendrive USB 3.0 de 16 GB',4990,35,10,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-102','Memoria USB 64GB','Pendrive USB 3.0 de 64 GB',8990,28,8,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-103','Memoria USB 128GB','Pendrive USB 3.0 de 128 GB',14990,17,5,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-104','Memoria microSD 32GB','Tarjeta microSD de 32 GB',5990,26,8,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-105','Memoria microSD 64GB','Tarjeta microSD de 64 GB',8990,23,7,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-106','Memoria microSD 128GB','Tarjeta microSD de 128 GB',14990,15,5,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-107','Tarjeta SD 64GB','Tarjeta SD para cámara de 64 GB',10990,12,4,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-108','Tarjeta SD 128GB','Tarjeta SD para cámara de 128 GB',17990,8,3,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-109','SSD SATA 240GB','Unidad de estado sólido SATA de 240 GB',29990,10,3,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-110','SSD SATA 480GB','Unidad de estado sólido SATA de 480 GB',39990,9,3,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-111','SSD SATA 1TB','Unidad de estado sólido SATA de un TB',69990,5,2,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-112','SSD NVMe 500GB','Unidad SSD NVMe M.2 de 500 GB',49990,8,3,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-113','SSD NVMe 1TB','Unidad SSD NVMe M.2 de un TB',89990,4,2,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-114','Disco externo 1TB','Disco portátil USB de un TB',59990,7,2,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-115','Disco externo 2TB','Disco portátil USB de dos TB',79990,6,2,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-116','Disco interno 1TB','Disco duro SATA de un TB',49990,8,3,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-117','Disco interno 2TB','Disco duro SATA de dos TB',69990,5,2,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-118','Carcasa externa 2.5 pulgadas','Gabinete USB para disco SATA de 2.5 pulgadas',12990,13,4,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-119','Carcasa SSD M.2','Gabinete USB para unidad SSD M.2',16990,9,3,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-120','Lector SD USB 3.0','Lector USB 3.0 para tarjetas SD y microSD',8990,16,5,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-121','Memoria USB metálica 32GB','Pendrive metálico USB 3.0 de 32 GB',7990,20,6,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-122','Memoria USB doble USB-C','Pendrive con conectores USB-A y USB-C',11990,11,3,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-123','SSD portátil 1TB','Unidad SSD externa portátil de un TB',99990,3,1,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-124','Disco externo 4TB','Disco portátil USB de cuatro TB',119990,2,1,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('ALM-125','SSD NVMe 2TB','Unidad SSD NVMe M.2 de dos TB',149990,2,1,(SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento'));

-- Comprobar los productos insertados
SELECT COUNT(*) AS total_productos FROM productos;

SELECT p.codigo, p.nombre, c.nombre AS categoria,
       p.precio, p.stock_actual, p.stock_minimo, p.estado
FROM productos p
INNER JOIN categorias c ON p.id_categoria = c.id_categoria
WHERE p.codigo REGEXP '^(PER|AUD|CAB|ALM)-1[0-9]{2}$'
ORDER BY p.codigo;