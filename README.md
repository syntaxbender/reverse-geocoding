# Reverse Geocoding
- epsg4623 koordinat sisteminde gelen koordinat noktalarına göre türkiye sınırları içerisinde *il ilçe bölge* bilgisi döndürür.
- veriler osm'den alınmış ve işlenmiştir.
- phpmyadmin export dosyası olan sql dosyası import edilmelidir.

## Verileri nasıl oluşturdum?
- OpenStreetMap verilerini kısmı olarak belirli sorgular(Overpass Query) çerçevesinde Overpass API ile alabilmekteyiz.
- Overpass API GUI arayüzü olan https://overpass-turbo.eu/ ile hızlıca istediğimiz sorguları çalıştırıp ihtiyacımız olan verileri alabilmekteyiz.
- Türkiye idari sınırlarını almak için gerekli query;
```
[out:json];
(area["name"="Türkiye"] -> .a;);
(rel(area)["type"="boundary"]["boundary"="administrative"];);
out geom;
```
- Overpass turbo ile osmtogeojson kütüphanesi arkaplanda tarayıcı tabanlı olarak çalışıp geojson formatında aldığımız çıktıyı veritabanına import etmeliyiz.
- Fakat bu işlem öncesinde veritabanı yükünü hafifletmek amacıyla geojson minifier işlevini sağlayan bir kütüphane ile veriyi minify etmek yararlı olabilir. bu işlem için mapshaper kullanabiliriz.
```
mapshaper osm_turkey.geojson -simplify dp 20% keep-shapes -o format=geojson precision=0.001 turkey.geojson

```
- Terminalde benzer parametreler içeren, %80 kayıplı veri, 3 ondalıklı basamağa sahip koordinatlar oluşturmayı ifade eden komut işimizi bir hayli görecektir.(detaylı bilgi için mapshaper dökümanına göz atabilirsiniz.)
- Bu işlemden sonra geojson verisini veritabanına import etmek kalıyor.
- Import işlemi için şu repoya göz atabilirsiniz. https://github.com/syntaxbender/Overpass-OSM-MySQL

## Dipnot
- OSM'den aldığımız data'da bazı ilçelerin relationları sorunluydu.
- Hatalı ilçelerin ilişkileri export ettiğim sql dosyasında düzeltilmiş durumdadır.
- Ayrıca Türkiye sınırları dışından da birkaç il ilçe de mevcuttu onlar da export edilen sql dosyasında temizlenmiş durumdadır.
## Teşekkür
- İzzet Kılıç'a araştırma sürecimi hızlandırmasındaki katkılarından dolayı teşekkür ediyorum. (https://github.com/izzetkalic/geojsons-of-turkey)

## Kaynaklar ve notlar

### pip algosu yazsak?
- https://www.toptal.com/python/computational-geometry-in-python-from-theory-to-implementation
- http://www.plumislandmedia.net/mysql/haversine-mysql-nearest-loc/ (kafa açıcı) (pdf olarak da ekledim)
### MySQL'de pip için çözüm
- https://dev.mysql.com/doc/refman/8.0/en/spatial-types.html
### Diğer Kaynaklar
- https://mysqlserverteam.com/spatial-reference-systems-in-mysql-8-0/
- https://mysqlserverteam.com/upgrading-spatial-indexes-to-mysql-8-0/
- https://dev.mysql.com/doc/refman/8.0/en/creating-spatial-indexes.html
- https://dev.mysql.com/doc/refman/5.7/en/spatial-geojson-functions.html
