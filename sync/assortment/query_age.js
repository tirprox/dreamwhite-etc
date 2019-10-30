db.retaildemand.aggregate([
  {
    $lookup: {
      from: "counterparty",
      localField: "agent.id",
      foreignField: "id",
      as: "counterparty",
    },
  },
  {
    $unwind: "$counterparty",
  },
  {
    $unwind: "$counterparty.attributes",
  },
  {
    $match: {
      "retailstore.meta.href": "https://online.moysklad.ru/api/remap/1.1/entity/retailstore/ba03c6d8-7161-11e8-9ff4-34e80003eb04",
      "counterparty.attributes.name": "Дата рождения",
    },
  },
  {
    $project: {
      "year": {
        $year: {
          $dateFromString: {
            dateString: "$counterparty.attributes.value",
          },
        },
      },
      "updated": 1,
      "_id": 0,
    },
  },
  {"$group" : {
      _id: {
        "year": { "$substr": [ "$updated", 0, 4 ] },
        "month": { "$substr": [ "$updated", 5, 2 ] },
        "yearOfBirth": "$year"
      },
      
      count:{$sum:1}}
    },
  {
    $project: {
      "year":  "$_id.year",
      "month":  "$_id.month",
      "yearOfBirth":  "$_id.yearOfBirth",
      "count":  1,
      "_id": 0,
    },
  },
])