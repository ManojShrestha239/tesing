//     let LocationDatas = [
//             {
//                 info: '<strong>Everest Colour</strong><br>\
//                 Abukhairani Outlet<br>\
//                 Samyog Communication and Suppliers - 9804164860',
//                 lat: 27.907704,
//                 long: 84.510140,
//                 province: 'Gandaki Province',
//                 district: 'tanahu'   
//             },
//             {
//                 info: '<strong>Everest Colour</strong><br>\
//                 Abukhairani Outlet<br>\
//                 Samyog Communication and Suppliers - 9804164860',
//                 lat: 27.907704,
//                 long: 84.510140,
//                 province: 'Gandaki Province',
//                 district: 'tanahu'   
//             },
//             {
//                 info: '<strong>Everest Colour</strong><br>\
//                 Abukhairani Outlet<br>\
//                 Samyog Communication and Suppliers - 9804164860',
//                 lat: 27.907704,
//                 long: 84.510140,
//                 province: 'Gandaki Province',
//                 district: 'tanahu'   
//             },

//             {
//                 info: '<strong>Everest Colour</strong><br>\
//                         Pipara Outlet<br>\
//                         Advance Colour House - 9852063503<br>\
//                         Chaudhary Hardware Pasal - 9815350521',
//                 lat: 26.639780,
//                 long: 87.190892,
//                 province: 'Province 1',
//                 district: 'Sunsari'
//             }
//     ];

let locations = [];
const fetchPromise = fetch("/mapdata");
fetchPromise.then(response => {
    return response.json();
}).then(data => {
    if (data.length > 0) {
        data.map((datas, indx) => {
            let location = [];
            location.push(datas.info, datas.lat, datas.long, datas.province, datas.district)
            locations.push(location);
        });
        if (locations.length > 0) {
            initMap();
        }
    }
})

function initMap() {

    //  MAPS FOR DEALER 
    var map = new google.maps.Map(document.getElementById('map-container'), {
        zoom: 7,
        center: new google.maps.LatLng(28.5038131, 84.1154335),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow({});

    var marker, i;

    for (i = 0; i < locations.length; i++) {
        //     locationInformation.push(locations[i][0]);
        //     infoSplitter(locations[i][0]);
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
    }

}

let dealer = document.getElementById('contact-cards');
let province = document.getElementById('province');
let district = document.getElementById('district');

//     function infoSplitter(information) {
//         let splitter = information.split("<br>");

//         for (let i = 2; i < splitter.length; i++) {
//             let info = splitter[i].split("-");
//             let division = document.createElement('div');
//             division.classList.add('maps-contact-cards');
//             division.id = "contact-cards";
//             let para = document.createElement('p');
//             para.id = "dealer";
//             para.innerText = info[0];
//             division.append(para);
//             let para1 = document.createElement('p');
//             para1.id = "address";
//             para1.innerText = splitter[1];
//             division.append(para1);
//             let para2 = document.createElement('p');
//             para2.id = "contact";
//             para2.innerText = info[1];
//             division.append(para2);
//             dealer.appendChild(division);
//         }
//     }



function dealerSelect() {
    let provinceName = province.value;
    if (!provinceName) {
        initMap();
    }
    let districtData = [];
    let filteredProvince = locations.filter((items, index) => {
        return items[3] == provinceName
    })

    //remove existing list of dealers in left maps
    let listOfClass = document.querySelectorAll('.maps-contact-cards');
    listOfClass.forEach((item) => {
        item.parentNode.removeChild(item);
    })

    //add list to the left maps
    filteredProvince.map((item) => {
        //     infoSplitter(item[0]);
        districtData.push(item[4]);
    })

    //set district data from filteredProvince
    let district = document.getElementById('district');
    let districts = [...new Set(districtData)];
    let districtList = document.querySelectorAll('.district-data');
    districtList.forEach((item) => {
        item.parentNode.removeChild(item);
    })
    districts.map((item) => {
        let division = document.createElement('option');
        division.classList = "district-data";
        division.innerHTML = item;
        district.appendChild(division);
    })


    //  MAPS FOR DEALER 
    var map = new google.maps.Map(document.getElementById('map-container'), {
        zoom: 7,
        center: new google.maps.LatLng(28.5038131, 84.1154335),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow({});

    var marker, i;

    for (i = 0; i < filteredProvince.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(filteredProvince[i][1], filteredProvince[i][2]),
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infowindow.setContent(filteredProvince[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
    }


}

function districtSelect() {
    let DistrictName = district.value;
    let filteredDistrict = locations.filter((items, index) => {
        return items[4] == DistrictName
    });
    //remove existing list of dealers in left maps
    let listOfClass = document.querySelectorAll('.maps-contact-cards');
    listOfClass.forEach((item) => {
        item.parentNode.removeChild(item);
    });
    //add list to the left maps
    filteredDistrict.map((item) => {
        //     infoSplitter(item[0]);
    });

    //  MAPS FOR DEALER 
    var map = new google.maps.Map(document.getElementById('map-container'), {
        zoom: 7,
        center: new google.maps.LatLng(28.5038131, 84.1154335),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow({});

    var marker, i;

    for (i = 0; i < filteredDistrict.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(filteredDistrict[i][1], filteredDistrict[i][2]),
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infowindow.setContent(filteredDistrict[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
    }


}