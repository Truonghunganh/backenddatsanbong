<?php


namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class QuanTableSeeder extends Seeder
{
    public function run()
    {
        //1
        $data = [
            "name" => "Sân bóng - Đại học bách khoa",
            "image"=>"image/Quan/mu.jpg",
            "address"=>"60-Ngô Sĩ liên-Đà nẵng",
            "phone" => "0812250590",
            "linkaddress" => "https://www.google.com/maps/place/S%C3%A2n+b%C3%B3ng+-+%C4%90%E1%BA%A1i+h%E1%BB%8Dc+b%C3%A1ch+khoa,+Ho%C3%A0+Kh%C3%A1nh+B%E1%BA%AFc,+Li%C3%AAn+Chi%E1%BB%83u,+%C4%90%C3%A0+N%E1%BA%B5ng+550000,+Vi%E1%BB%87t+Nam/@16.0736302,108.1521688,17z/data=!3m1!4b1!4m5!3m4!1s0x314218d763876437:0x8b64a3ef3c6b3e58!8m2!3d16.0737726!4d108.1543554?hl=vi-VN",
            "vido" => "16.0737726",
            "kinhdo" => "108.1543554",
            "trangthai" =>true,
            "review"=>0,
            "Create_time" => Carbon::now()
        ];
        DB::table('quans')->insert($data);
        //2
        $data = [
            "name" => "Sân bóng đá Chuyên Việt",
            "image" => "image/Quan/hunganh.jpg",
            "address" => "98 Tiểu La, Hòa Thuận Đông, Hải Châu, Đà Nẵng",
            "phone" => "0812250590",
            "linkaddress" => "https://www.google.com/maps/dir/16.0732797,108.1526873/Sân+bóng+đá+Chuyên+Việt,+98+Tiểu+La,+Hòa+Thuận+Đông,+Hải+Châu,+Đà+Nẵng+550000,+Việt+Nam/@16.0507264,108.148344,13z/data=!3m1!4b1!4m9!4m8!1m1!4e1!1m5!1m1!1s0x314219c0801817c3:0x1702bb03f6985b2f!2m2!1d108.214369!2d16.0451026?hl=vi-VN",
            "vido" => "16.0451026",
            "kinhdo" => "108.214369",
            "trangthai" => true,
            "review" => 0,
            "Create_time" => Carbon::now()
        ];
        
        DB::table('quans')->insert($data);
        //3
        $data = [
            "name" => "Sân bóng đá Chuyên Việt",
            "image" => "image/Quan/a.jpg",
            "address" => "H28 Âu Cơ, Hoà Khánh Bắc, Liên Chiểu, Đà Nẵng ",
            "phone" => "0337265910",
            "linkaddress" => "https://www.google.com/maps/dir/16.0732797,108.1526873/Sân+Bóng+Đá+Chuyên+Việt,+Âu+Cơ,+Hòa+Khánh+Bắc,+Liên+Chiểu,+Đà+Nẵng/@16.0734531,108.1431804,16z/data=!3m1!4b1!4m9!4m8!1m1!4e1!1m5!1m1!1s0x3142192aa90315ad:0x8f7bdad44ca1fa4a!2m2!1d108.1434063!2d16.0706259?hl=vi-VN",
            "vido" => "16.0706259",
            "kinhdo" => "108.1434063",
            "trangthai" => true,
            "review" => 0,
            "Create_time" => Carbon::now()
        ];
        DB::table('quans')->insert($data);
        //4
        $data = [
            "name" => "Sân Bóng Đá Đạt Phúc",
            "image" => "image/Quan/b.jpg",
            "address" => "19 Phạm Như Xương, Hoà Khánh Nam, Liên Chiểu, Đà Nẵng",
            "phone" => "0337265910",
            "linkaddress" => "https://www.google.com/maps/place/Sân+Bóng+Đá+Đạt+Phúc/@16.0628798,108.1553149,18z/data=!3m1!4b1!4m13!1m7!3m6!1s0x31421925c109130d:0x110d3d79bf5249da!2zMTkgUGjhuqFtIE5oxrAgWMawxqFuZywgSG_DoCBLaMOhbmggTmFtLCBMacOqbiBDaGnhu4N1LCDEkMOgIE7hurVuZyA1NTAwMDAsIFZp4buHdCBOYW0!3b1!8m2!3d16.0628772!4d108.1564092!3m4!1s0x31421925c109130d:0x4bacd1f6e3df235d!8m2!3d16.0628772!4d108.1564092?hl=vi-VN",
            "vido" => "16.0628772",
            "kinhdo" => "108.1564092",
            "trangthai" => true,
            "review" => 0,
            "Create_time" => Carbon::now()
        ];
        DB::table('quans')->insert($data);
        //5
        $data = [
            "name" => "Sân Bóng Manchester United",
            "image" => "image/Quan/c.jpg",
            "address" => "59 Ngô Thì Nhậm, Hoà Khánh Nam, Liên Chiểu, Đà Nẵng 550000, Việt Nam",
            "phone" => "0354658717",
            "linkaddress" => "https://www.google.com/maps/place/Sân+Bóng+Manchester+United/@16.070761,108.1542209,21z/data=!4m13!1m7!3m6!1s0x314218d7f0da97ff:0x2d96e2ad56a12368!2zNjEgTmfDtCBUaMOsIE5o4bqtbSwgSG_DoCBLaMOhbmggTmFtLCBMacOqbiBDaGnhu4N1LCDEkMOgIE7hurVuZyA1NTAwMDAsIFZp4buHdCBOYW0!3b1!8m2!3d16.0707349!4d108.1543061!3m4!1s0x314218d82271624f:0xe435529625132578!8m2!3d16.0708556!4d108.1544331?hl=vi-VN",
            "vido" => "16.0708556",
            "kinhdo" => "108.1544331",
            "trangthai" => true,
            "review" => 0,
            "Create_time" => Carbon::now()
        ];
        DB::table('quans')->insert($data);
        //6
        $data = [
            "name" => "Sân Bóng Đá Nam Cao",
            "image" => "image/Quan/d.jpg",
            "address" => "169 Đ. Nam Cao -Hoà Khánh Nam -Liên Chiểu Đà Nẵng",
            "phone" => "0356899335",
            "linkaddress" => "https://www.google.com/maps/place/Sân+Bóng+Đá+Nam+Cao/@16.063911,108.1455569,17z/data=!4m13!1m7!3m6!1s0x3142192c0e642f17:0x12442573c37593ad!2zMTY5IMSQLiBOYW0gQ2FvLCBIb8OgIEtow6FuaCBOYW0sIExpw6puIENoaeG7g3UsIMSQw6AgTuG6tW5nIDU1MDAwMCwgVmnhu4d0IE5hbQ!3b1!8m2!3d16.0639059!4d108.1477456!3m4!1s0x3142199289c312f7:0x23704af6ca5703fd!8m2!3d16.0633224!4d108.1491009?hl=vi-VN",
            "vido" => "16.0633224",
            "kinhdo" => "108.1491009",
            "trangthai" => true,
            "review" => 0,
            "Create_time" => Carbon::now()
        ];
        DB::table('quans')->insert($data);
        //7
        $data = [
            "name" => "Sân Bóng Đá Nguyễn Chánh",
            "image" => "image/Quan/e.jpg",
            "address" => "86 Nguyễn Chánh, Hoà Khánh Bắc, Liên Chiểu, Đà Nẵng ",
            "phone" => "0356899336",
            "linkaddress" => "https://www.google.com/maps/place/86+Nguyễn+Chánh,+Hoà+Khánh+Bắc,+Liên+Chiểu,+Đà+Nẵng+550000,+Việt+Nam/@16.0842914,108.1487053,17z/data=!4m13!1m7!3m6!1s0x314218ce5871addf:0x41806cf5617e3407!2zODYgTmd1eeG7hW4gQ2jDoW5oLCBIb8OgIEtow6FuaCBC4bqvYywgTGnDqm4gQ2hp4buDdSwgxJDDoCBO4bq1bmcgNTUwMDAwLCBWaeG7h3QgTmFt!3b1!8m2!3d16.0842863!4d108.150894!3m4!1s0x314218ce5871addf:0x41806cf5617e3407!8m2!3d16.0842863!4d108.150894?hl=vi-VN",
            "vido" => "16.0842863",
            "kinhdo" => "108.150894",
            "trangthai" => true,
            "review" => 0,
            "Create_time" => Carbon::now()
        ];
        DB::table('quans')->insert($data);
        //8
        $data = [
            "name" => "Sân bóng Thanh Thanh",
            "image" => "image/Quan/f.jpg",
            "address" => "79 Ngô Văn Sơ, Hoà Khánh Bắc, Liên Chiểu, Đà Nẵng ",
            "phone" => "0787179937",
            "linkaddress" => "https://www.google.com/maps/place/Sân+bóng+Thanh+Thanh/@16.0676506,108.1471403,18z/data=!4m5!3m4!1s0x314219297ceb4635:0xe4aab088d70be14!8m2!3d16.0669193!4d108.1490714?hl=vi-VN",
            "vido" => "16.0669193",
            "kinhdo" => "108.1490714",
            "trangthai" => true,
            "review" => 0,
            "Create_time" => Carbon::now()
        ];
        DB::table('quans')->insert($data);
        //9
        $data = [
            "name" => "Sân Bóng Đá Ngọc Thạch",
            "image" => "image/Quan/g.jpg",
            "address" => "207 Phạm Như Xương, Hoà Khánh Nam, Liên Chiểu, Đà Nẵng ",
            "phone" => "0935291246",
            "linkaddress" => "https://www.google.com/maps/place/207+Phạm+Như+Xương,+Hoà+Khánh+Nam,+Liên+Chiểu,+Đà+Nẵng+550000,+Việt+Nam/@16.0656956,108.1477859,19z/data=!4m13!1m7!3m6!1s0x3142192be4ec3be3:0x11eaae64126c9f6e!2zMjA3IFBo4bqhbSBOaMawIFjGsMahbmcsIEhvw6AgS2jDoW5oIE5hbSwgTGnDqm4gQ2hp4buDdSwgxJDDoCBO4bq1bmcgNTUwMDAwLCBWaeG7h3QgTmFt!3b1!8m2!3d16.0655835!4d108.1483143!3m4!1s0x3142192be4ec3be3:0x11eaae64126c9f6e!8m2!3d16.0655835!4d108.1483143?hl=vi-VN",
            "vido" => "16.0655835",
            "kinhdo" => "108.1483143",
            "trangthai" => true,
            "review" => 0,
            "Create_time" => Carbon::now()
        ];
        DB::table('quans')->insert($data);
        //10
        $data = [
            "name" => "Sân Bóng đá Trưng Vương",
            "image" => "image/Quan/h.jpg",
            "address" => "403 Trưng Nữ Vương, Hòa Thuận Nam, Hải Châu, Đà Nẵng",
            "phone" => "0812250590",
            "linkaddress" => "https://www.google.com/maps/place/Sân+Bóng+đá+Trưng+Vương/@16.0462637,108.2082137,17z/data=!4m13!1m7!3m6!1s0x314219bee5e41971:0x9a171ad90134e854!2zNTYwIFRyxrBuZyBO4buvIFbGsMahbmcsIEjDsmEgVGh14bqtbiBOYW0sIEjhuqNpIENow6J1LCDEkMOgIE7hurVuZyA1NTAwMDAsIFZp4buHdCBOYW0!3b1!8m2!3d16.0462586!4d108.2104024!3m4!1s0x314219bee68e5add:0xe5e9b113bc37fd33!8m2!3d16.0471609!4d108.2100709?hl=vi-VN",
            "vido" => "16.0471609",
            "kinhdo" => "108.2100709",
            "trangthai" => false,
            "review" => 0,
            "Create_time" => Carbon::now()
        ];
        DB::table('quans')->insert($data);
        
        $names = ["Khu Bóng Đá Thủy Lợi", "Sân bóng đá Phương Tuấn-Hội An", "Sân vận động Hội An"
            ,"Sân vận động Cẩm Châu", "Sân Bóng Đá Cỏ Tự Nhiên"];
        $images = ['image/Quan/hunganh.jpg', 'image/Quan/i.jpg', 'image/Quan/k.jpg', 'image/Quan/m.jpg', 'image/Quan/n.jpg'];
        $diachis = ["24 Phan Bá Phiến, Tân An, Hội An, Quảng Nam", "02B Thái Phiên, Phường Minh An, Hội An, Quảng Nam 560000, Việt Nam", "18 Lý Thường Kiệt, Sơn Phong, Hội An, Quảng Nam, Việt Nam",
            "Cẩm Châu, Hội An, Quảng Nam, Việt Nam", "168 Nguyễn Trãi, Tây Lộc, Thành phố Huế, Thừa Thiên Huế, Việt Nam"];
        $phones = ["0374894200", "0374894201", "0374894202", "0374894203", "0374894204"];
        
        $linkaddress = [
            "https://www.google.com/maps/place/24+Phan+Bá+Phiến,+Tân+An,+Hội+An,+Quảng+Nam,+Việt+Nam/@15.8777916,108.3194347,17z/data=!4m13!1m7!3m6!1s0x31420e70f774b22f:0x9481a7e45e60d5c1!2zMjQgUGhhbiBCw6EgUGhp4bq_biwgVMOibiBBbiwgSOG7mWkgQW4sIFF14bqjbmcgTmFtLCBWaeG7h3QgTmFt!3b1!8m2!3d15.8871269!4d108.3241001!3m4!1s0x31420e70f774b22f:0x9481a7e45e60d5c1!8m2!3d15.8871269!4d108.3241001?hl=vi-VN",
            "https://www.google.com/maps/place/Sân+bóng+đá+Phương+Tuấn-Hội+An/@15.881449,108.3268398,17z/data=!4m13!1m7!3m6!1s0x31420e791a6930ff:0x18f239fe251c1720!2zMiBUaMOhaSBQaGnDqm4sIFBoxrDhu51uZyBNaW5oIEFuLCBI4buZaSBBbiwgUXXhuqNuZyBOYW0sIFZp4buHdCBOYW0!3b1!8m2!3d15.8814439!4d108.3290285!3m4!1s0x0:0x8792a03837874555!8m2!3d15.8811413!4d108.3314402?hl=vi-VN",
            "https://www.google.com/maps/place/Sân+vận+động+Hội+An/@15.881449,108.3268398,17z/data=!4m13!1m7!3m6!1s0x31420e791a6930ff:0x18f239fe251c1720!2zMiBUaMOhaSBQaGnDqm4sIFBoxrDhu51uZyBNaW5oIEFuLCBI4buZaSBBbiwgUXXhuqNuZyBOYW0sIFZp4buHdCBOYW0!3b1!8m2!3d15.8814439!4d108.3290285!3m4!1s0x31420e78f76c34bf:0xfd515cbe1ce08065!8m2!3d15.8818761!4d108.3308107?hl=vi-VN",
            "https://www.google.com/maps/place/Sân+vận+động+Cẩm+Châu,+Cẩm+Châu,+Hội+An,+Quảng+Nam,+Việt+Nam/@15.8816799,108.3425788,17z/data=!4m13!1m7!3m6!1s0x31420e791a6930ff:0x18f239fe251c1720!2zMiBUaMOhaSBQaGnDqm4sIFBoxrDhu51uZyBNaW5oIEFuLCBI4buZaSBBbiwgUXXhuqNuZyBOYW0sIFZp4buHdCBOYW0!3b1!8m2!3d15.8814439!4d108.3290285!3m4!1s0x31420dd04ee838e7:0x30b2fe82d10b9b07!8m2!3d15.8817063!4d108.3428624?hl=vi-VN",
            "https://www.google.com/maps/place/Sân+Bóng+Đá+Cỏ+Tự+Nhiên/@16.4724469,107.5684767,17z/data=!4m8!1m2!2m1!1zc8OibiBiw7NuZyDhu58gSHXhur8!3m4!1s0x0:0xbbb0edcac33334b1!8m2!3d16.4734158!4d107.5682927?hl=vi-VN"

        ];
        $vidos=["15.8871269", "15.8811413", "15.8818761", "15.8817063", "16.4734158"];
        $kinhdos = ["108.3241001", "108.3314402", "108.3308107", "108.3428624", "107.5682927"];
        for ($i=0; $i < 5; $i++) {
            $data = [
                "name" => $names[$i],
                "image" => $images[$i],
                "address" => $diachis[$i],
                "phone" => $phones[$i],
                "linkaddress" => $linkaddress[$i],
                "vido" => $vidos[$i],
                "kinhdo" => $kinhdos[$i],
                "trangthai" => true,
                "review" => 0,
                "Create_time" => Carbon::now()
            ];
            DB::table('quans')->insert($data);
            $quan= DB::table('quans')->where("phone",$phones[$i])->first();
            $data = [
                "name" => "Sân A",
                "idquan" => $quan->id,
                "numberpeople" => 11,
                "priceperhour" => 300000,
                "trangthai" => true,
                "Create_time" => Carbon::now()
            ];
            DB::table('sans')->insert($data);
            $data = [
                "name" => "Sân B",
                "idquan" =>$quan->id,
                "numberpeople" => 5,
                "priceperhour" => 150000,
                "trangthai" => true,
                "Create_time" => Carbon::now()
            ];
            DB::table('sans')->insert($data);
            $data = [
                "name" => "Sân C",
                "idquan" => $quan->id,
                "numberpeople" => 7,
                "priceperhour" => 200000,
                "trangthai" => true,
                "Create_time" => Carbon::now()
            ];
            DB::table('sans')->insert($data);
        
        }

    }
}
