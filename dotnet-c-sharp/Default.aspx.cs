using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Xml;
using System.Xml.XPath;
using System.Data.SqlClient;
using System.Data;

public partial class _Default : System.Web.UI.Page
{
    /// <summary>
    /// ***************************************************  Copyright 2010, Janrain, Inc. All Rights Reserved **********************************************************
    /// 
    /// - This Janrain Engage Starter Kit is offered as an open and unsupported library for Engage customers   
    /// 
    /// 
    /// To get started, make sure you drop this project into your localhost IIS and configure it as an Application.  Then in the Engage
    /// application dashboard do the following:
    /// 1) Go into the Sign-in Setup (where you likely got this code)
    /// 2) Where it says "Get the Widget", select embedded widget and get the link from the iFrame src parameter and break it into the 
    ///    two variables below
    /// 3) Modify the ApiKey to reflect YOUR ApiKey from the Engage Dashboard
    /// 4) If you want to see contacts from Facebook, uncomment the "Contacts" block below
    /// 5) The engage.cs is comprised of 2 core classes EngageService and EngageUser.
    ///     - EngageService - This will handle connecting to the Engage service and getting the user payload which is supplied to the EngageUser
    ///                       class for parameterizing the values returned from Engage.  You will also use this service to get back the contacts
    ///                       from Engage for this authenticated user.
    ///     - EngageUser - Represents a user object based on the data returned from Engage.  ///                      
    /// 
    /// ************************************************************************************************************************************************
    /// </summary>
    
    EngageService LocalEngageService = null;
    
    protected void Page_Load(object sender, EventArgs e)
    {
        string TokenValue       = Request["token"];                                         //Get token returned from Engage
        string ApiKey           = "8c04fd573bd8d27d5ec72eda1d04c5f82909c6c6";               //Unique Engage API Key
        string EngageAppURL     = "https://jbtest.rpxnow.com/openid/v2/signin?token_url=";  //The Engage Application URL that you get from the Sign in Setup
        string EngageTokenURL   = "http%3A%2F%2Flocalhost%2Fengagedotnet%2Fdefault.aspx";   //The URL Encoded token URL that you get from the Sign in Setup
        bool ShowContactsFlag   = true;                                                    //To show the contacts for this user based on the network that they logged in with
        bool SendStatusUpdate   = false;                                                    //To show an API level social publish using default "canned" data
        string ContactProviders = "Facebook,Google,LinkedIn";                               //This is the list of providers that you can get contacts from

        EngageSignInLink.HRef = EngageAppURL + EngageTokenURL;                              //Full signin link to start the sign in process
        
        //For this starter, the Default.aspx page is the initiating page AND the token URL
        //So, we must check to see if there's a token that's being posted or not
        if (TokenValue != null)
        {
            LocalEngageService = new EngageService(ApiKey);

           

            
            //Get the user profile based on the returned token.            
            EngageUser User = GetUserData(TokenValue);
            //Show the user data
            if(User != null)
                ShowUserData(User);

            #region Social Publishing API
            //Make an API level wall update.  Use with Facebook as best example!
            //NOTE: This uses a pre-formatted JSON object pulled from documentation on rpxnow.com/docs#api_activity.  Clearly you would want to format
            //this according to REAL data.
            /*
                {
                    "user_generated_content": "I thought you would appreciate my review.",
                    "title": "A Critique of Atomic Pizza",
                    "action_links": [
                    {
                        "href": "http:\/\/example.com\/review\/write",
                        "text": "Write a review"
                    }
                    ],
                    "action": "wrote a review of Atomic Pizza",
                    "url": "http:\/\/example.com\/reviews\/12345\/",
                    "media": [
                    {
                        "href": "http:\/\/bit.ly\/3fkBwe",
                        "src": "http:\/\/bit.ly\/1nmIX9",
                        "type": "image"
                    }
                    ],
                    "description": "Atomic Pizza has a great atmosphere and great prices.",
                    "properties": {
                    "Location": {
                        "href": "http:\/\/bit.ly\/3fkBwe",
                        "text": "North Portland"
                    },
                    "Rating": "5 Stars"
                    }
                }
            */
            #endregion

            if (SendStatusUpdate)
            {
                string StatusMessage = "{\"user_generated_content\": \"I thought you would appreciate my review.\",\"title\": \"A Critique of Atomic Pizza\",\"action_links\": [{\"href\": \"http:\\/\\/example.com\\/review\\/write\",\"text\": \"Write a review\"}],\"action\": \"wrote a review of Atomic Pizza\",\"url\": \"http:\\/\\/example.com\\/reviews\\/12345\\/\",\"media\": [{\"href\": \"http:\\/\\/bit.ly\\/3fkBwe\",\"src\": \"http:\\/\\/bit.ly\\/1nmIX9\",\"type\": \"image\"}],\"description\": \"Atomic Pizza has a great atmosphere and great prices.\",\"properties\": {\"Location\": {\"href\": \"http:\\/\\/bit.ly\\/3fkBwe\",\"text\": \"North Portland\"},\"Rating\": \"5 Stars\"}}";
                LocalEngageService.SetActivity(User.Identifier, StatusMessage);
            }
            
            
            //Get Contacts for the user based on page setup
            if (ShowContactsFlag)
            {
                //If the provider in the list of providers matches the provider that the user logged in with then
                //get the contacts for that provider
                string[] providerList = ContactProviders.Split(',');
                foreach(string specificProvider in providerList)
                {
                    if (User.Provider == specificProvider)
                    {
                        ShowContacts(GetUserContacts(User));
                        break;
                    }

                }     
            }
        }
    }





    #region public pagelevel methods

    /// <summary>
    /// Create a service object and return the user data
    /// </summary>
    /// <param name="APIKey">String value representing Engage API Key</param>
    /// <param name="EngageToken">Sring value representing returned Engage Token</param>
    /// <returns>EngageUser object</returns>
    public EngageUser GetUserData(string EngageToken)
    { 
        return LocalEngageService.AuthInfo(EngageToken);
    }

    /// <summary>
    /// This will display the user payload returned from Engage
    /// </summary>
    /// <param name="User" type="EngageService">Represents the returned user profile</param>
    public void ShowUserData(EngageUser User)
    {
        //Make our display containers visible
        BodyContainer.Visible       = true;
        EngageProvider.Visible      = true;
        EngageAccessToken.Visible   = true;
        EngageUserName.Visible      = true;
        EngageEmail.Visible         = true;
        EngageIdentifier.Visible    = true;
        EngageFirstName.Visible     = true;
        EngageLastName.Visible      = true;
        if (User.Photo != null)
            EngagePhoto.Visible = true;

        // Get Profile Data and show on page 
        EngageProvider.InnerHtml    += User.Provider;
        EngageAccessToken.InnerHtml += User.ProviderAccessToken;
        EngageUserName.InnerHtml    += User.UserName;
        EngageEmail.InnerHtml       += User.Email;
        EngageIdentifier.InnerHtml  += User.Identifier;
        EngageFirstName.InnerHtml   += User.FirstName;
        EngageLastName.InnerHtml    += User.LastName;
        HeaderName.Text             = User.FirstName + " " + User.LastName;
        if (User.Photo != null)
            UserPhoto.ImageUrl = User.Photo;

    }


    /// <summary>
    /// Get the list of contacts from a users selected provider
    /// </summary>
    /// <param name="User">The authenticated Engage User object</param>
    /// <returns>An XmlDocument object from Engage</returns>
    public XmlDocument GetUserContacts(EngageUser User)
    {
       XmlDocument contacts = LocalEngageService.GetContacts(Server.HtmlEncode(User.Identifier));
       return contacts;

    }

    /// <summary>
    /// Show the contacts on the display
    /// </summary>
    /// <param name="EngageContacts">An </param>
    public void ShowContacts(XmlDocument EngageContacts)
    {
        //Show div that our XML control is in and bind to the control
        EngageUserContacts.Visible = true;
        Contacts.XPathNavigator = EngageContacts.CreateNavigator();
        Contacts.DataBind();
    }

    #endregion
}
