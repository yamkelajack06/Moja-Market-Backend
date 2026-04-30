<?php
    class Item {
        private string $itemID;
        private string $userID;
        private string $datePosted;
        private string $itemName;
        private string $itemDescription;
        private string $condition;
        private string $sellerLocation;
        private string $stockStatus;
        private int $quantity;
        private float $price;
        private float $averageRating;
        private string $itemImage;

        function __construct(
            string $itemID,
            string $userID,
            string $datePosted,
            string $itemName,
            string $itemDescription,
            string $condition,
            string $sellerLocation,
            string $stockStatus,
            int $quantity,
            float $price,
            float $averageRating,
            string $itemImage
        ) {
            $this->itemID = $itemID;
            $this->userID = $userID;
            $this->datePosted = $datePosted;
            $this->itemName = $itemName;
            $this->itemDescription = $itemDescription;
            $this->condition = $condition;
            $this->sellerLocation = $sellerLocation;
            $this->stockStatus = $stockStatus;
            $this->quantity = $quantity;
            $this->price = $price;
            $this->averageRating = $averageRating;
            $this->itemImage = $itemImage;
        }
    }