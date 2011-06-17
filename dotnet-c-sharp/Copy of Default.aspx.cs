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
    
    EngageService EngageService = null;

    
    
    protected void Page_Load(object sender, EventArgs e)
    {
        string TokenValue       = Request["token"];                                         //Get token returned from Engage
        string ApiKey           = "fcda9b168be8888bdc9c9288aab4e1572fe86ff7";               //Unique Engage API Key
        string EngageAppURL     = "https://login.disturbedasylum.com/openid/v2/signin?token_url=";  //The Engage Application URL that you get from the Sign in Setup
        string EngageTokenURL   = "http%3A%2F%2Flocalhost%2Fengagedotnet%2Fdefault.aspx";   //The URL Encoded token URL that you get from the Sign in Setup
        bool ShowContactsFlag   = true;                                                    //To show the contacts for this user based on the network that they logged in with
        string ContactProviders = "Facebook,Google,LinkedIn";                               //This is the list of providers that you can get contacts from

        EngageSignInLink.HRef = EngageAppURL + EngageTokenURL;                              //Full signin link to start the sign in process

        EngageService = new EngageService(ApiKey);

        //For this starter, the Default.aspx page is the initiating page AND the token URL
        //So, we must check to see if there's a token that's being posted or not
        if (TokenValue != null)
        {
            EngageService = new EngageService(ApiKey);
            
            //Get the user profile based on the returned token.            
            //EngageUser User = GetUserData(TokenValue);
            //Show the user data
            //if(User != null)
             //   ShowUserData(User);

            
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

        ShowContacts(GetUserContacts("https://www.google.com/profiles/107150545288686948275"));
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
        return EngageService.AuthInfo(EngageToken);
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
       XmlDocument contacts = EngageService.GetContacts(Server.HtmlEncode(User.Identifier));
       return contacts;

    }



    public XmlDocument GetUserContacts(string UserInfo)
    {
        XmlDocument contacts = EngageService.GetContacts(Server.HtmlEncode(UserInfo));
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
